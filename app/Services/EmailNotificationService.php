<?php

namespace App\Services;

use App\Jobs\SendEmailJob;
use App\Models\EmailLog;
use Illuminate\Mail\Mailable;

class EmailNotificationService
{
    /**
     * Enviar correo de Confirmación de Appointment (Envío asíncrono inmediato)
     *
     * @param int|null $clienteId
     * @param string $correoDestino
     * @param Mailable $mailable
     * @return void
     */
    public function sendCitaNotification($clienteId, string $correoDestino, Mailable $mailable): void
    {
        $emailLog = EmailLog::create([
            'customer_id' => $clienteId,
            'tipo_notificacion' => 'cita',
            'correo_destino' => $correoDestino,
            'status' => 'Pendiente',
        ]);

        SendEmailJob::dispatch($emailLog->id, $mailable)->onQueue('emails');
    }

    /**
     * Enviar correo de Receta Médica (Envío asíncrono con retraso de 10 minutos)
     * Para consolidar notificaciones de Historias Clínicas (SOAP)
     *
     * @param int|null $clienteId
     * @param string $correoDestino
     * @param Mailable $mailable
     * @return void
     */
    public function sendRecetaNotification($clienteId, string $correoDestino, Mailable $mailable): void
    {
        $emailLog = EmailLog::create([
            'customer_id' => $clienteId,
            'tipo_notificacion' => 'receta',
            'correo_destino' => $correoDestino,
            'status' => 'Pendiente',
        ]);

        SendEmailJob::dispatch($emailLog->id, $mailable)
            ->delay(now()->addMinutes(10))
            ->onQueue('emails');
    }

    /**
     * Enviar correo de Boleta de Pago / POS (Envío asíncrono inmediato)
     *
     * @param int|null $clienteId
     * @param string $correoDestino
     * @param Mailable $mailable
     * @return void
     */
    public function sendPagoNotification($clienteId, string $correoDestino, Mailable $mailable): void
    {
        $emailLog = EmailLog::create([
            'customer_id' => $clienteId,
            'tipo_notificacion' => 'pago',
            'correo_destino' => $correoDestino,
            'status' => 'Pendiente',
        ]);

        SendEmailJob::dispatch($emailLog->id, $mailable)->onQueue('emails');
    }
}
