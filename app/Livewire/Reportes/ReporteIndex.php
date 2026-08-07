<?php

namespace App\Livewire\Reportes;

use App\Models\Appointment;
use App\Models\Product;
use App\Models\Sale;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Reportes y Estadísticas')]
class ReporteIndex extends Component
{
    public string $periodo = 'mes_actual'; // hoy, semana_actual, mes_actual, año_actual
    
    public function render()
    {
        $startDate = match($this->periodo) {
            'hoy' => Carbon::today(),
            'semana_actual' => Carbon::now()->startOfWeek(),
            'mes_actual' => Carbon::now()->startOfMonth(),
            'año_actual' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        // 1. Métricas de Ventas
        $ventasPeriodo = Sale::where('status', 'COMPLETED')
            ->where('created_at', '>=', $startDate)
            ->sum('total');
            
        $ticketPromedio = Sale::where('status', 'COMPLETED')
            ->where('created_at', '>=', $startDate)
            ->avg('total') ?? 0;

        // 2. Métricas de Citas
        $citasNuevas = Appointment::where('created_at', '>=', $startDate)->count();
        $citasCompletadas = Appointment::where('status', 'COMPLETADA')
            ->where('fecha_hora', '>=', $startDate)
            ->count();
            
        $citasCanceladas = Appointment::where('status', 'CANCELADA')
            ->where('fecha_hora', '>=', $startDate)
            ->count();

        // 3. Métricas de Inventario
        $productosStockBajo = Product::where('type', '!=', 'SERVICIO')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count();
        
        return view('livewire.reportes.reporte-index', [
            'ventasPeriodo' => $ventasPeriodo,
            'ticketPromedio' => $ticketPromedio,
            'citasNuevas' => $citasNuevas,
            'citasCompletadas' => $citasCompletadas,
            'citasCanceladas' => $citasCanceladas,
            'productosStockBajo' => $productosStockBajo,
        ]);
    }
}
