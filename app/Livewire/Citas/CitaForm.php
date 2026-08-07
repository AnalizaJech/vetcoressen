<?php

namespace App\Livewire\Citas;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Formulario de cita con validación de horarios
#[Layout('components.layouts.app')]
#[Title('Cita')]
class CitaForm extends Component
{
    public ?int $citaId = null;
    public ?string $cliente_id = null;
    public ?string $mascota_id = null;
    public ?string $veterinario_id = null;
    public ?string $fecha = null;
    public ?string $hora = null;
    public ?string $motivo = null;
    public ?string $estado = 'PENDIENTE';
    public ?string $notas = null;

    protected function rules(): array
    {
        return [
            'cliente_id'      => 'required|exists:customers,id',
            'mascota_id'      => 'required|exists:pets,id',
            'veterinario_id'  => 'required|exists:users,id',
            'fecha'           => 'required|date',
            'hora'            => 'required|date_format:H:i',
            'motivo'          => 'nullable|string|max:500',
            'estado'          => 'required|in:PENDIENTE,CONFIRMADA,EN_PROGRESO,COMPLETADA,CANCELADA,EMERGENCIA',
            'notas'           => 'nullable|string|max:500',
        ];
    }

    public function mount(?int $id = null): void
    {
        if ($id) {
            $cita = Appointment::findOrFail($id);
            $this->citaId = $cita->id;
            $this->cliente_id = (string) $cita->customer_id;
            $this->mascota_id = (string) $cita->pet_id;
            $this->veterinario_id = (string) $cita->veterinarian_id;
            $this->fecha = $cita->fecha_hora->format('Y-m-d');
            $this->hora = $cita->fecha_hora->format('H:i');
            $this->motivo = $cita->reason ?? '';
            $this->estado = $cita->status;
            $this->notas = $cita->notes ?? '';
        } else {
            $this->fecha = request('fecha', now()->format('Y-m-d'));
            $this->hora = request('hora', '09:00');
        }
    }

    // Cargar mascotas del cliente seleccionado
    public function updatedClienteId(): void
    {
        $this->mascota_id = '';
    }

    public function guardar()
    {
        $this->validate();
        
        $fecha_hora = $this->fecha . ' ' . $this->hora;

        // Validar que no haya conflicto de horario con el mismo veterinario, a menos que sea EMERGENCIA
        if ($this->estado !== 'EMERGENCIA') {
            $conflicto = Appointment::where('veterinarian_id', $this->veterinario_id)
                ->where('fecha_hora', $fecha_hora)
                ->whereNotIn('status', ['CANCELADA', 'COMPLETADA'])
                ->when($this->citaId, fn ($q) => $q->where('id', '!=', $this->citaId))
                ->exists();

            if ($conflicto) {
                $this->addError('hora', 'El veterinario ya tiene una cita en ese horario. Use "EMERGENCIA" para forzar.');
                return;
            }
        }

        $datos = [
            'clinic_id'        => 1, // Por ahora quemado, asumiendo clínica principal
            'customer_id'      => $this->cliente_id,
            'pet_id'      => $this->mascota_id,
            'veterinarian_id'  => $this->veterinario_id,
            'fecha_hora'      => $fecha_hora,
            'reason'          => $this->motivo ?: 'Sin motivo especificado', // Evitar null
            'status'          => $this->estado,
            'notes'           => $this->notas ?: null,
        ];

        if ($this->citaId) {
            $cita = Appointment::findOrFail($this->citaId);
            $cita->update($datos);
            session()->flash('mensaje', 'Cita actualizada correctamente.');
        } else {
            $cita = Appointment::create($datos);
            session()->flash('mensaje', 'Cita agendada correctamente.');

            // Enviar notificación por correo al cliente
            $cliente = \App\Models\Customer::find($this->cliente_id);
            if ($cliente && $cliente->email) {
                app(\App\Services\EmailNotificationService::class)->sendCitaNotification(
                    $cliente->id,
                    $cliente->email,
                    new \App\Mail\CitaMail($cita)
                );
            }

            // Enviar notificación por correo al veterinario
            if ($this->veterinario_id) {
                $veterinario = \App\Models\User::find($this->veterinario_id);
                if ($veterinario && $veterinario->email) {
                    app(\App\Services\EmailNotificationService::class)->sendCitaNotification(
                        null,
                        $veterinario->email,
                        new \App\Mail\CitaMail($cita)
                    );
                }
            }
        }

        return redirect()->route('citas.index');
    }

    public function guardarEmergencia(\App\Services\CitasService $citasService)
    {
        $this->validate([
            'cliente_id' => 'required|exists:customers,id',
            'mascota_id' => 'required|exists:pets,id',
        ]);

        $citasService->crearEmergenciaAbsoluta((int) $this->cliente_id, (int) $this->mascota_id, 1);
        
        session()->flash('mensaje', 'Cita de EMERGENCIA registrada y alerta enviada.');
        return redirect()->route('citas.index');
    }


    public function render()
    {
        $clientes = Customer::where('is_active', true)->orderBy('first_name')->get();
        $mascotas = $this->cliente_id
            ? Pet::with('especie')->where('customer_id', $this->cliente_id)->where('fallecido', false)->orderBy('name')->get()
            : collect();
        $veterinarios = User::role('veterinario')->orderBy('name')->get();

        $horasOcupadas = [];
        if ($this->veterinario_id && $this->fecha) {
            $horasOcupadas = Appointment::where('veterinarian_id', $this->veterinario_id)
                ->whereDate('fecha_hora', $this->fecha)
                ->whereNotIn('status', ['CANCELADA', 'COMPLETADA'])
                ->when($this->citaId, fn($q) => $q->where('id', '!=', $this->citaId))
                ->get()
                ->map(fn($cita) => $cita->fecha_hora->format('H:i'))
                ->toArray();
        }

        $horas = [];
        for ($i = 8; $i <= 18; $i++) {
            $h = str_pad($i, 2, '0', STR_PAD_LEFT);
            
            $slot1 = "$h:00";
            if (!in_array($slot1, $horasOcupadas)) {
                $horas[] = ['value' => $slot1, 'label' => $slot1];
            }
            
            $slot2 = "$h:30";
            if (!in_array($slot2, $horasOcupadas)) {
                $horas[] = ['value' => $slot2, 'label' => $slot2];
            }
        }

        return view('livewire.citas.cita-form', [
            'clientes'     => $clientes,
            'mascotas'     => $mascotas,
            'veterinarios' => $veterinarios,
            'horas'        => $horas,
        ]);
    }
}
