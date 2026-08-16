<?php

namespace App\Jobs;

use App\Mail\WelcomeEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Queueable;

    // Máximo 1 intento para evitar reintentos innecesarios si el mailer falla
    public int $tries = 1;

    public string $emailDestino;
    public string $nombreCompleto;
    public string $tipoPersona;

    /**
     * Create a new job instance.
     */
    public function __construct(string $emailDestino, string $nombreCompleto, string $tipoPersona = 'Cliente')
    {
        $this->emailDestino = $emailDestino;
        $this->nombreCompleto = $nombreCompleto;
        $this->tipoPersona = $tipoPersona;
    }

    /**
     * Execute the job.
     * Protegido con try-catch: Resend en testing solo permite enviar al email verificado
     */
    public function handle(): void
    {
        try {
            Mail::to($this->emailDestino)->send(
                new WelcomeEmail($this->nombreCompleto, $this->tipoPersona)
            );
        } catch (\Exception $e) {
            Log::warning("Email de bienvenida no enviado a {$this->emailDestino}: " . $e->getMessage());
        }
    }
}

