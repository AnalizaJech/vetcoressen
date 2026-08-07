<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CitasService
{
    /**
     * Crea una cita de emergencia absoluta que salta restricciones.
     * Queda como 'Fantasma' (sin veterinario) o en estado 'EMERGENCIA'
     * y es visible para todos los veterinarios.
     */
    public function crearEmergenciaAbsoluta(int $clienteId, int $mascotaId, int $clinicaId)
    {
        $cita = Appointment::create([
            'customer_id' => $clienteId,
            'pet_id' => $mascotaId,
            'clinic_id' => $clinicaId,
            'veterinarian_id' => null, // Esperando adopción
            'fecha_hora' => now(),
            'status' => 'EMERGENCIA',
            'reason' => 'EMERGENCIA - Requiere atención inmediata',
            'notes' => 'Cita creada por emergencia absoluta',
        ]);

        // Aquí iría un broadcast (Pusher/Echo) para alertar a los paneles
        // event(new \App\Events\EmergenciaCreada($cita));

        return $cita;
    }

    /**
     * Reasignación masiva de citas de un veterinario a otro en un día específico.
     */
    public function reasignarCitasMasivamente(int $veterinarioOrigenId, int $veterinarioDestinoId, string $fecha)
    {
        $citas = Appointment::where('veterinarian_id', $veterinarioOrigenId)
            ->whereDate('fecha_hora', $fecha)
            ->whereIn('status', ['PENDIENTE', 'CONFIRMADA'])
            ->get();

        $count = 0;

        foreach ($citas as $cita) {
            $cita->update([
                'veterinarian_id' => $veterinarioDestinoId,
                'notes' => $cita->notes . "\n[Reasignada por sistema]"
            ]);

            // Enviar SMS a través de Twilio
            $cliente = Customer::find($cita->customer_id);
            if ($cliente && $cliente->phone) {
                // Aquí iría la integración real con Twilio
                Log::info("Simulando SMS Twilio a {$cliente->phone}: Su cita fue reasignada al veterinario ID: {$veterinarioDestinoId}.");
            }

            $count++;
        }

        return $count;
    }
}
