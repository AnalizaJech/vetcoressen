<?php

namespace App\Livewire\Citas;

use App\Models\Appointment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Listado de citas con filtro por estado, vista lista/calendario
#[Layout('components.layouts.app')]
#[Title('Citas')]
class CitaIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';

    #[Url]
    public string $filtroEstado = '';

    #[Url]
    public string $filtroFecha = '';

    #[Url]
    public string $filtroHora = '';

    #[Url]
    public string $filtroVeterinario = '';

    #[Url]
    public string $filtroCliente = '';

    #[Url]
    public string $filtroMascota = '';

    // Toggle de vista: 'calendario' (default) o 'lista'
    #[Url]
    public string $vistaActiva = 'calendario';

    // ID de la cita pendiente de eliminar (modal de confirmación)
    public ?int $citaEliminarId = null;
    public ?Appointment $citaVer = null;

    public function ver(int $id): void
    {
        $this->citaVer = Appointment::findOrFail($id);
    }

    public function confirmDeletion(int $id): void
    {
        $this->citaEliminarId = $id;
    }

    // Campos para reasignación masiva
    public string $reasignar_fecha = '';
    public string $reasignar_origen = '';
    public string $reasignar_destino = '';

    // Campos para Cita de Emergencia
    public string $emergencia_cliente_id = '';
    public string $emergencia_mascota_id = '';

    public function updatedBusqueda(): void
    {
        $this->resetPage();
        $this->dispatch('calendar-refresh');
    }

    public function updatedFiltroEstado(): void
    {
        $this->resetPage();
        $this->dispatch('calendar-refresh');
    }
    
    public function updatedFiltroFecha(): void { $this->resetPage(); $this->dispatch('calendar-refresh'); }
    public function updatedFiltroHora(): void { $this->resetPage(); $this->dispatch('calendar-refresh'); }
    public function updatedFiltroVeterinario(): void { $this->resetPage(); $this->dispatch('calendar-refresh'); }
    public function updatedFiltroCliente(): void { $this->resetPage(); $this->dispatch('calendar-refresh'); }
    public function updatedFiltroMascota(): void { $this->resetPage(); $this->dispatch('calendar-refresh'); }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'filtroEstado', 'filtroFecha', 'filtroHora', 'filtroVeterinario', 'filtroCliente', 'filtroMascota']);
        $this->resetPage();
        $this->dispatch('calendar-refresh');
    }

    // Cambiar estado de una cita rápidamente
    public function cambiarEstado(int $id, string $nuevoEstado): void
    {
        $cita = Appointment::findOrFail($id);
        $cita->update(['status' => $nuevoEstado]);
        session()->flash('mensaje', "Cita actualizada a «{$nuevoEstado}».");
    }

    public function eliminar(): void
    {
        if (!$this->citaEliminarId) return;
        
        $cita = Appointment::findOrFail($this->citaEliminarId)->delete();
        session()->flash('mensaje', 'Cita eliminada correctamente.');
    }

    public function reasignarMasivo(\App\Services\CitasService $citasService): void
    {
        $this->validate([
            'reasignar_fecha' => 'required|date',
            'reasignar_origen' => 'required|exists:users,id',
            'reasignar_destino' => 'required|exists:users,id|different:reasignar_origen',
        ]);

        $count = $citasService->reasignarCitasMasivamente(
            (int) $this->reasignar_origen,
            (int) $this->reasignar_destino,
            $this->reasignar_fecha
        );

        $this->reset(['reasignar_origen', 'reasignar_destino', 'reasignar_fecha']);
        session()->flash('mensaje', "Se reasignaron $count citas exitosamente.");
    }

    public function crearEmergencia(\App\Services\CitasService $citasService)
    {
        $this->validate([
            'emergencia_cliente_id' => 'required|exists:customers,id',
            'emergencia_mascota_id' => 'required|exists:pets,id',
        ]);

        $clinicaId = 1; // Para fines de demostración asumo que la sucursal o clínica principal es 1

        $cita = $citasService->crearEmergenciaAbsoluta(
            (int) $this->emergencia_cliente_id,
            (int) $this->emergencia_mascota_id,
            $clinicaId
        );

        $this->reset(['emergencia_cliente_id', 'emergencia_mascota_id']);
        session()->flash('mensaje', 'Cita de Emergencia creada exitosamente.');
        
        return $this->redirectRoute('citas.index', navigate: true);
    }

    /**
     * Retorna citas para FullCalendar en formato de eventos JSON.
     * Se llama desde JS via Livewire.$wire.getCitasCalendario(start, end).
     */
    #[\Livewire\Attributes\Renderless]
    public function getCitasCalendario(string $start, string $end): array
    {
        $startDate = \Carbon\Carbon::parse($start)->toDateTimeString();
        $endDate = \Carbon\Carbon::parse($end)->toDateTimeString();

        $query = Appointment::select('id', 'fecha_hora', 'end_time', 'status', 'reason', 'customer_id', 'pet_id', 'veterinarian_id')
            ->with(['cliente:id,first_name,last_name', 'mascota:id,name', 'veterinario:id,name'])
            ->whereBetween('fecha_hora', [$startDate, $endDate])
            ->when($this->filtroEstado, fn ($q) => $q->where('status', $this->filtroEstado))
            ->when($this->filtroVeterinario, fn ($q) => $q->where('veterinarian_id', $this->filtroVeterinario))
            ->when($this->filtroCliente, fn ($q) => $q->where('customer_id', $this->filtroCliente))
            ->when($this->filtroMascota, fn ($q) => $q->where('pet_id', $this->filtroMascota));

        // Mapeo de colores por estado (coincide con los badge-* del sistema)
        $colorMap = [
            'PENDIENTE' => ['bg' => '#f59e0b', 'border' => '#d97706', 'text' => '#ffffff'],
            'CONFIRMADA' => ['bg' => '#3b82f6', 'border' => '#2563eb', 'text' => '#ffffff'],
            'EN_PROGRESO' => ['bg' => '#8b5cf6', 'border' => '#7c3aed', 'text' => '#ffffff'],
            'COMPLETADA' => ['bg' => '#10b981', 'border' => '#059669', 'text' => '#ffffff'],
            'CANCELADA' => ['bg' => '#ef4444', 'border' => '#dc2626', 'text' => '#ffffff'],
            'EMERGENCIA' => ['bg' => '#ef4444', 'border' => '#b91c1c', 'text' => '#ffffff'],
        ];

        return $query->get()->map(function ($cita) use ($colorMap) {
            $colors = $colorMap[$cita->status] ?? ['bg' => '#6b7280', 'border' => '#4b5563', 'text' => '#ffffff'];
            
            $start = $cita->fecha_hora ? $cita->fecha_hora->format('Y-m-d\TH:i:s') : '';
            $end = $cita->end_time 
                ? \Carbon\Carbon::parse($cita->end_time)->format('Y-m-d\TH:i:s') 
                : ($cita->fecha_hora ? $cita->fecha_hora->copy()->addMinutes(30)->format('Y-m-d\TH:i:s') : '');

            return [
                'id' => $cita->id,
                'title' => ($cita->mascota?->name ?? 'Sin mascota') . ' — ' . ($cita->reason ?? 'Sin motivo'),
                'start' => $start,
                'end' => $end,
                'backgroundColor' => $colors['bg'],
                'borderColor' => $colors['border'],
                'textColor' => $colors['text'],
                'extendedProps' => [
                    'status' => $cita->status,
                    'cliente' => $cita->cliente?->nombre_completo ?? '-',
                    'mascota' => $cita->mascota?->name ?? '-',
                    'veterinario' => $cita->veterinario?->name ?? '-',
                    'reason' => $cita->reason ?? '-',
                    'editUrl' => route('citas.editar', $cita->id),
                ],
            ];
        })->toArray();
    }

    /**
     * Actualiza fecha/hora de una cita (desde drag & drop de FullCalendar).
     */
    public function moverCita(int $id, string $newStart): void
    {
        $cita = Appointment::findOrFail($id);
        $cita->update(['fecha_hora' => $newStart]);
        $this->dispatch('notify', type: 'success', message: 'Cita reprogramada correctamente.');
    }

    public function render()
    {
        $citas = Appointment::with(['cliente', 'mascota', 'veterinario'])
            ->when($this->filtroEstado, fn ($q) => $q->where('status', $this->filtroEstado))
            ->when($this->filtroFecha, fn ($q) => $q->whereDate('fecha_hora', $this->filtroFecha))
            ->when($this->filtroHora, fn ($q) => $q->whereTime('fecha_hora', $this->filtroHora . ':00'))
            ->when($this->filtroVeterinario, fn ($q) => $q->where('veterinarian_id', $this->filtroVeterinario))
            ->when($this->filtroCliente, fn ($q) => $q->where('customer_id', $this->filtroCliente))
            ->when($this->filtroMascota, fn ($q) => $q->where('pet_id', $this->filtroMascota))
            ->when($this->busqueda, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('reason', 'like', "%{$this->busqueda}%")
                        ->orWhereHas('cliente', fn ($c) =>
                            $c->where('first_name', 'like', "%{$this->busqueda}%")
                              ->orWhere('last_name', 'like', "%{$this->busqueda}%")
                        )
                        ->orWhereHas('mascota', fn ($m) =>
                            $m->where('name', 'like', "%{$this->busqueda}%")
                        );
                });
            })
            ->orderBy('fecha_hora')
            ->paginate(15);

        return view('livewire.citas.cita-index', [
            'citas' => $citas,
            'veterinarios' => \App\Models\User::role('veterinario')->get(),
            'clientes' => \App\Models\Customer::select(['id', 'first_name', 'last_name'])->limit(50)->get(),
            'mascotas' => \App\Models\Pet::select(['id', 'name'])->limit(50)->get(),
        ]);
    }
}
