<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckLateAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'citas:check-late';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica las citas pendientes y las marca como EXCEDIDO si pasaron más de 15 minutos de su hora de inicio.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ahora = Carbon::now();
        $limiteTolerancia = $ahora->copy()->subMinutes(15);
        $fechaHoy = $ahora->toDateString();

        // Buscamos citas pendientes de hoy cuya hora de inicio sea anterior a la tolerancia (hace más de 15 min)
        $citasTardias = Appointment::where('fecha', $fechaHoy)
            ->where('estado', 'PENDIENTE')
            ->where('hora', '<=', $limiteTolerancia->toTimeString())
            ->get();

        $count = 0;

        foreach ($citasTardias as $cita) {
            $cita->update(['estado' => 'EXCEDIDO']);
            $count++;
            
            Log::info("Appointment ID {$cita->id} marcada como EXCEDIDO por impuntualidad mayor a 15 min. Customer ID: {$cita->cliente_id}");
        }

        $this->info("Se actualizaron {$count} citas a estado EXCEDIDO.");
    }
}
