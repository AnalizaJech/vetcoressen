<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Dashboard principal - KPIs reales, últimas ventas, gráfico semanal
#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $hoy = Carbon::today();

        // KPI: Ingresos del día (ventas con estado pagado/completado)
        $ingresosDia = Sale::whereDate('created_at', $hoy)
            ->where('status', 'PAGADO')
            ->sum('total');

        // KPI: Citas pendientes de hoy
        $citasPendientes = Appointment::whereDate('fecha_hora', $hoy)
            ->whereIn('status', ['PENDIENTE', 'CONFIRMADA'])
            ->count();

        // KPI: Alertas de inventario (stock actual <= stock mínimo)
        $productosEnAlerta = Product::where('is_active', true)
            ->where('type', '!=', 'Servicio')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->get();
        $alertasInventario = $productosEnAlerta->count();

        // KPI: Lotes próximos a vencer (90 días)
        $lotesProximosVencer = \App\Models\ProductBatch::with('product')
            ->where('fecha_vencimiento', '<=', now()->addDays(90))
            ->where('stock_actual', '>', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        // KPI: Mascotas internadas (sin campo directo, usar citas en progreso como proxy)
        $internados = Appointment::where('status', 'EN_PROGRESO')
            ->whereDate('fecha_hora', '<=', $hoy)
            ->count();

        // Últimas 5 ventas
        $ultimasVentas = Sale::with(['cliente', 'cajero'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Gráfico semanal - ingresos de los últimos 7 días
        $diasAtras = Carbon::today()->subDays(6);
        $ventasAgrupadas = Sale::where('status', 'PAGADO')
            ->whereDate('created_at', '>=', $diasAtras)
            ->selectRaw('DATE(created_at) as fecha, SUM(total) as suma')
            ->groupBy('fecha')
            ->pluck('suma', 'fecha');

        $ingresosSemana = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i);
            $fechaStr = $dia->toDateString();
            $total = $ventasAgrupadas->get($fechaStr, 0);

            $ingresosSemana->push([
                'dia'   => $dia->translatedFormat('D'),
                'date' => $dia->format('d/m'),
                'total' => (float) $total,
            ]);
        }

        // Valor máximo para escalar el gráfico
        $maxIngreso = $ingresosSemana->max('total') ?: 1;

        // Estadísticas rápidas para el panel inferior
        $totalClientes = Customer::where('is_active', true)->count();
        $totalMascotas = Pet::where('fallecido', false)->count();

        // Obtener tipo de cambio USD a PEN
        // Obtener tipo de cambio USD a PEN con fallback para evitar S/ 0.00
        $tipoCambio = app(\App\Services\CurrencyService::class)->getExchangeRate('PEN', 'USD') ?? 3.75;

        // Próximas citas de hoy (para acceso rápido en dashboard)
        $citasHoy = Appointment::with(['mascota', 'veterinario'])
            ->whereDate('fecha_hora', $hoy)
            ->whereIn('status', ['PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO'])
            ->orderBy('fecha_hora')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', [
            'ingresosDia'       => $ingresosDia,
            'citasPendientes'   => $citasPendientes,
            'alertasInventario' => $alertasInventario,
            'productosEnAlerta' => $productosEnAlerta,
            'lotesProximosVencer' => $lotesProximosVencer,
            'internados'        => $internados,
            'ultimasVentas'     => $ultimasVentas,
            'ingresosSemana'    => $ingresosSemana,
            'maxIngreso'        => $maxIngreso,
            'totalClientes'     => $totalClientes,
            'totalMascotas'     => $totalMascotas,
            'tipoCambio'        => $tipoCambio,
            'citasHoy'          => $citasHoy,
        ]);
    }
}
