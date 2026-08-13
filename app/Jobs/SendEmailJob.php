<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El número de veces que el job puede intentarse.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * El número de segundos a esperar antes de volver a intentar el job.
     *
     * @var int
     */
    public $backoff = 60;

    protected $emailLogId;
    protected $mailable;

    /**
     * Create a new job instance.
     */
    public function __construct($emailLogId, Mailable $mailable)
    {
        $this->emailLogId = $emailLogId;
        $this->mailable = $mailable;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailLog = EmailLog::find($this->emailLogId);

        if (!$emailLog || $emailLog->status === 'Fallido') {
            return;
        }

        try {
            Mail::to($emailLog->correo_destino)->send($this->mailable);

            // Si el correo se envía correctamente, actualizamos el estado
            $emailLog->update([
                'status' => 'Enviado',
                'fecha_envio' => now(),
            ]);

        } catch (\Exception $e) {
            // Registramos el error de forma temporal (el job se reintentará)
            $emailLog->update([
                'error_mensaje' => $e->getMessage(),
            ]);

            if (config('queue.default') === 'sync') {
                // En modo sync, simplemente llamamos a failed para no crashear la petición
                $this->failed($e);
                return;
            }

            throw $e; // Lanzamos la excepción para que Laravel reintente el job en colas asíncronas
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $emailLog = EmailLog::find($this->emailLogId);

        if ($emailLog) {
            $emailLog->update([
                'status' => 'Fallido',
                'error_mensaje' => $exception->getMessage(),
            ]);

            // Inactivar el envío automático a este cliente
            if ($emailLog->customer_id) {
                $cliente = Customer::find($emailLog->customer_id);
                if ($cliente) {
                    $cliente->update(['email_valido' => false]);
                }
            }

            // Notificar al Superadmin (obtenido mediante spatie/laravel-permission)
            $superAdmins = User::role('super_admin')->get();
            
            if ($superAdmins->isNotEmpty()) {
                Log::warning("Fallo en envío de correo a cliente_id: {$emailLog->customer_id}, email: {$emailLog->correo_destino}. Favor de verificar.");
                // Si usas Database Notifications de Laravel:
                // $superAdmin->notify(new EmailFailedNotification($emailLog));
            }
        }
    }
}
