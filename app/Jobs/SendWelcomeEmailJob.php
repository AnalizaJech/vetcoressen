<?php

namespace App\Jobs;

use App\Mail\WelcomeEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Queueable;

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
     */
    public function handle(): void
    {
        Mail::to($this->emailDestino)->send(
            new WelcomeEmail($this->nombreCompleto, $this->tipoPersona)
        );
    }
}
