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
    public string $filtroTiempo = 'semana'; // 'hoy', 'semana', 'mes', 'anio'
    public string $filtroTiempoCitas = 'hoy'; // 'hoy', 'semana', 'mes', 'anio'

    public function render()
    {
        $hoy = Carbon::today();
        $ahora = Carbon::now();
        $enDosHoras = Carbon::now()->addHours(2);

        // Determinar fecha de inicio según filtro de ingresos
        $fechaInicio = match ($this->filtroTiempo) {
            'hoy' => Carbon::today(),
            'semana' => Carbon::today()->startOfWeek(),
            'mes' => Carbon::today()->startOfMonth(),
            'anio' => Carbon::today()->startOfYear(),
            default => Carbon::today()->subDays(6), // 'semana' móvil por defecto
        };

        // KPI: Ingresos (según filtro)
        $ingresosDia = Sale::whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $hoy)
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

        // Últimas 5 ventas (podrían filtrarse también, pero usualmente son solo las 'últimas')
        $ultimasVentas = Sale::with(['cliente', 'cajero'])
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $hoy)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Gráfico - ingresos del periodo seleccionado
        $ingresosGrafico = collect();
        if ($this->filtroTiempo === 'hoy') {
            $ventasAgrupadas = Sale::where('status', 'PAGADO')
                ->whereDate('created_at', $hoy)
                ->selectRaw('DATE_FORMAT(created_at, "%H:00") as fecha, SUM(total) as suma')
                ->groupBy('fecha')
                ->pluck('suma', 'fecha');

            for ($i = 0; $i < 24; $i++) {
                $horaStr = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $total = $ventasAgrupadas->get($horaStr, 0);
                $ingresosGrafico->push([
                    'dia'   => str_pad($i, 2, '0', STR_PAD_LEFT) . 'h',
                    'date' => $horaStr,
                    'total' => (float) $total,
                ]);
            }
        } elseif ($this->filtroTiempo === 'anio') {
            $diasAtras = Carbon::today()->startOfYear();
            $ventasAgrupadas = Sale::where('status', 'PAGADO')
                ->whereDate('created_at', '>=', $diasAtras)
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as fecha, SUM(total) as suma')
                ->groupBy('fecha')
                ->pluck('suma', 'fecha');
            
            for ($i = 1; $i <= 12; $i++) {
                $mes = Carbon::today()->startOfYear()->addMonths($i - 1);
                $fechaStr = $mes->format('Y-m');
                $total = $ventasAgrupadas->get($fechaStr, 0);

                $ingresosGrafico->push([
                    'dia'   => $mes->translatedFormat('M'),
                    'date' => $mes->format('m/Y'),
                    'total' => (float) $total,
                ]);
            }
        } elseif ($this->filtroTiempo === 'mes') {
            $diasAtras = Carbon::today()->startOfMonth();
            $diasIterar = Carbon::today()->daysInMonth;
            
            $ventasAgrupadas = Sale::where('status', 'PAGADO')
                ->whereDate('created_at', '>=', $diasAtras)
                ->whereDate('created_at', '<=', Carbon::today()->endOfMonth())
                ->selectRaw('DATE(created_at) as fecha, SUM(total) as suma')
                ->groupBy('fecha')
                ->pluck('suma', 'fecha');

            for ($i = 1; $i <= $diasIterar; $i++) {
                $dia = Carbon::today()->startOfMonth()->addDays($i - 1);
                $fechaStr = $dia->toDateString();
                $total = $ventasAgrupadas->get($fechaStr, 0);

                $ingresosGrafico->push([
                    'dia'   => $dia->format('d/m'),
                    'date' => $dia->format('d/m'),
                    'total' => (float) $total,
                ]);
            }
        } else {
            // Semana (por defecto últimos 7 días)
            $diasAtras = Carbon::today()->subDays(6);
            $ventasAgrupadas = Sale::where('status', 'PAGADO')
                ->whereDate('created_at', '>=', $diasAtras)
                ->selectRaw('DATE(created_at) as fecha, SUM(total) as suma')
                ->groupBy('fecha')
                ->pluck('suma', 'fecha');

            for ($i = 6; $i >= 0; $i--) {
                $dia = Carbon::today()->subDays($i);
                $fechaStr = $dia->toDateString();
                $total = $ventasAgrupadas->get($fechaStr, 0);

                $ingresosGrafico->push([
                    'dia'   => $dia->translatedFormat('D'),
                    'date' => $dia->format('d/m'),
                    'total' => (float) $total,
                ]);
            }
        }

        // Valor máximo para escalar el gráfico
        $maxIngreso = $ingresosGrafico->max('total') ?: 1;

        // Estadísticas rápidas para el panel inferior
        $totalClientes = Customer::where('is_active', true)->count();
        $totalMascotas = Pet::where('fallecido', false)->count();

        // Obtener tipo de cambio USD a PEN
        $tipoCambio = app(\App\Services\CurrencyService::class)->getExchangeRate('PEN', 'USD') ?? 3.75;

        // Citas (Basado en $filtroTiempoCitas)
        $fechaInicioCitas = match ($this->filtroTiempoCitas) {
            'hoy' => Carbon::today(),
            'semana' => Carbon::today()->startOfWeek(),
            'mes' => Carbon::today()->startOfMonth(),
            'anio' => Carbon::today()->startOfYear(),
            default => Carbon::today(),
        };

        $fechaFinCitas = match ($this->filtroTiempoCitas) {
            'hoy' => Carbon::today()->endOfDay(),
            'semana' => Carbon::today()->endOfWeek(),
            'mes' => Carbon::today()->endOfMonth(),
            'anio' => Carbon::today()->endOfYear(),
            default => Carbon::today()->endOfDay(),
        };

        $citasHoy = Appointment::with(['mascota', 'veterinario'])
            ->whereBetween('fecha_hora', [$fechaInicioCitas, $fechaFinCitas])
            ->whereIn('status', ['PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO'])
            ->orderBy('fecha_hora')
            ->get(); // Fetch all based on filter

        // Alerta de Citas Próximas (dentro de las próximas 2 horas)
        $citasProximas = Appointment::with(['mascota', 'veterinario'])
            ->whereBetween('fecha_hora', [$ahora, $enDosHoras])
            ->whereIn('status', ['PENDIENTE', 'CONFIRMADA'])
            ->orderBy('fecha_hora')
            ->get();

        return view('livewire.dashboard', [
            'ingresosDia'       => $ingresosDia,
            'citasPendientes'   => $citasPendientes,
            'alertasInventario' => $alertasInventario,
            'productosEnAlerta' => $productosEnAlerta,
            'lotesProximosVencer' => $lotesProximosVencer,
            'internados'        => $internados,
            'ultimasVentas'     => $ultimasVentas,
            'ingresosSemana'    => $ingresosGrafico,
            'maxIngreso'        => $maxIngreso,
            'totalClientes'     => $totalClientes,
            'totalMascotas'     => $totalMascotas,
            'tipoCambio'        => $tipoCambio,
            'citasHoy'          => $citasHoy,
            'citasProximas'     => $citasProximas,
        ]);
    }
}
