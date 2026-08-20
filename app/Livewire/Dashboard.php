<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Dashboard principal - Enfocado en KPIs clínicos, predicción de enfermedades y síntomas
#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public string $filtroTiempo = 'semana'; // 'hoy', 'semana', 'mes', 'anio' (Ingresos)
    public string $filtroTiempoCitas = 'hoy'; // 'hoy', 'semana', 'mes', 'anio' (Lista de citas)
    public string $filtroAtenciones = 'semana'; // 'hoy', 'semana', 'mes', 'anio' (Gráfico de atenciones)

    public array $atencionesGrafico = [];
    public array $ingresosSemana = [];

    public function mount(): void
    {
        $fechaInicio = match ($this->filtroTiempo) {
            'hoy' => Carbon::today(),
            'semana' => Carbon::today()->startOfWeek(),
            'mes' => Carbon::today()->startOfMonth(),
            'anio' => Carbon::today()->startOfYear(),
            default => Carbon::today()->subDays(6),
        };
        $this->ingresosSemana = $this->obtenerIngresosGrafico($fechaInicio, Carbon::today())->toArray();
        $this->atencionesGrafico = $this->obtenerAtencionesGrafico();
    }

    public function updatedFiltroTiempo(): void
    {
        $fechaInicio = match ($this->filtroTiempo) {
            'hoy' => Carbon::today(),
            'semana' => Carbon::today()->startOfWeek(),
            'mes' => Carbon::today()->startOfMonth(),
            'anio' => Carbon::today()->startOfYear(),
            default => Carbon::today()->subDays(6),
        };
        $ingresosGrafico = $this->obtenerIngresosGrafico($fechaInicio, Carbon::today());
        $this->ingresosSemana = $ingresosGrafico->toArray();
        $this->dispatch('dashboard-charts-updated', [
            'atenciones' => $this->obtenerAtencionesGrafico(),
            'ingresos' => $this->ingresosSemana,
        ]);
    }

    public function updatedFiltroAtenciones(): void
    {
        $this->atencionesGrafico = $this->obtenerAtencionesGrafico();
        $fechaInicio = match ($this->filtroTiempo) {
            'hoy' => Carbon::today(),
            'semana' => Carbon::today()->startOfWeek(),
            'mes' => Carbon::today()->startOfMonth(),
            'anio' => Carbon::today()->startOfYear(),
            default => Carbon::today()->subDays(6),
        };
        $ingresosGrafico = $this->obtenerIngresosGrafico($fechaInicio, Carbon::today());
        $this->ingresosSemana = $ingresosGrafico->toArray();
        $this->dispatch('dashboard-charts-updated', [
            'atenciones' => $this->atencionesGrafico,
            'ingresos' => $this->ingresosSemana,
        ]);
    }

    public function updatedFiltroTiempoCitas(): void
    {
        // Se recalcula automáticamente en render()
    }

    public function render()
    {
        $clinicId = 1;
        $hoy = Carbon::today();
        $ahora = Carbon::now();
        $enDosHoras = Carbon::now()->addHours(2);

        // 1. KPI: Ingresos (según filtro)
        $fechaInicio = match ($this->filtroTiempo) {
            'hoy' => Carbon::today(),
            'semana' => Carbon::today()->startOfWeek(),
            'mes' => Carbon::today()->startOfMonth(),
            'anio' => Carbon::today()->startOfYear(),
            default => Carbon::today()->subDays(6),
        };

        $ingresosDia = Sale::where('created_at', '>=', $fechaInicio)
            ->where('created_at', '<=', $hoy->copy()->endOfDay())
            ->where('status', 'PAGADO')
            ->sum('total');

        // 2. KPI: Citas pendientes de hoy
        $citasPendientes = Appointment::whereDate('fecha_hora', $hoy)
            ->whereIn('status', ['PENDIENTE', 'CONFIRMADA'])
            ->count();

        // 3. KPI: Alertas de inventario (stock actual <= stock mínimo)
        $productosEnAlerta = Product::where('clinic_id', $clinicId)
            ->where('is_active', true)
            ->where('type', '!=', 'Servicio')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->get();
        $alertasInventario = $productosEnAlerta->count();

        // 4. KPI: Lotes próximos a vencer (90 días)
        $lotesProximosVencer = \App\Models\ProductBatch::with('product')
            ->whereHas('product', function ($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId)
                      ->where('is_active', true);
            })
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', now()->addDays(90))
            ->where('stock_actual', '>', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        // 5. KPI: Mascotas en consulta ahora (EN_PROGRESO)
        $internados = Appointment::where('status', 'EN_PROGRESO')
            ->whereDate('fecha_hora', '<=', $hoy)
            ->count();

        // 6. Citas programadas
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
            ->get();

        // Alerta de Citas Próximas (dentro de las próximas 2 horas)
        $citasProximas = Appointment::with(['mascota', 'cliente'])
            ->where('fecha_hora', '>=', clone $ahora)
            ->where('fecha_hora', '<=', clone $enDosHoras)
            ->whereIn('status', ['PENDIENTE', 'CONFIRMADA'])
            ->orderBy('fecha_hora')
            ->get();

        // Últimas 5 ventas
        $ultimasVentas = Sale::with(['cliente', 'cajero'])
            ->where('created_at', '>=', $fechaInicio)
            ->where('created_at', '<=', $hoy->copy()->endOfDay())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // 7. Gráfico: Atenciones Realizadas vs Por Realizar
        $this->atencionesGrafico = $this->obtenerAtencionesGrafico();

        // 8. Enfermedades más recurrentes con predicción y recomendaciones
        $enfermedadesTop = $this->obtenerEnfermedadesTop();

        // 9. Síntomas más recurrentes con recomendaciones de equipamiento
        $sintomasTop = $this->obtenerSintomasTop();

        // 10. Gráfico de ingresos del periodo
        $ingresosGrafico = $this->obtenerIngresosGrafico($fechaInicio, $hoy);
        $this->ingresosSemana = $ingresosGrafico->toArray();
        $maxIngreso = $ingresosGrafico->max('total') ?: 1;

        $totalClientes = Customer::where('is_active', true)->count();
        $totalMascotas = Pet::where('fallecido', false)->count();
        $tipoCambio = app(\App\Services\CurrencyService::class)->getExchangeRate('PEN', 'USD') ?? 3.75;
        $ventasHoy = Sale::whereDate('created_at', $hoy)->where('status', 'PAGADO')->sum('total');
        $totalVentasHoy = Sale::whereDate('created_at', $hoy)->where('status', 'PAGADO')->count();
        $citasHoyCount = Appointment::whereDate('fecha_hora', $hoy)->count();
        $citasCompletadasHoy = Appointment::whereDate('fecha_hora', $hoy)->where('status', 'COMPLETADA')->count();
        $cajaAbierta = \App\Models\CashRegister::where('status', 'ABIERTA')->first();

        return view('livewire.dashboard', [
            'ingresosDia'         => $ingresosDia,
            'ventasHoy'           => $ventasHoy,
            'totalVentasHoy'      => $totalVentasHoy,
            'citasHoyCount'       => $citasHoyCount,
            'citasCompletadasHoy' => $citasCompletadasHoy,
            'cajaAbierta'         => $cajaAbierta,
            'citasPendientes'     => $citasPendientes,
            'alertasInventario'   => $alertasInventario,
            'productosEnAlerta'   => $productosEnAlerta,
            'lotesProximosVencer' => $lotesProximosVencer,
            'internados'          => $internados,
            'ultimasVentas'       => $ultimasVentas,
            'ingresosSemana'      => $ingresosGrafico,
            'maxIngreso'          => $maxIngreso,
            'totalClientes'       => $totalClientes,
            'totalMascotas'       => $totalMascotas,
            'tipoCambio'          => $tipoCambio,
            'citasHoy'            => $citasHoy,
            'citasProximas'       => $citasProximas,
            'atencionesGrafico'   => $this->atencionesGrafico,
            'enfermedadesTop'     => $enfermedadesTop,
            'sintomasTop'         => $sintomasTop,
        ]);
    }

    private function obtenerAtencionesGrafico(): array
    {
        $realizadas = [];
        $pendientes = [];
        $labels = [];
        $keys = [];

        if ($this->filtroAtenciones === 'hoy') {
            // Agrupar por bloques de horas de hoy
            $horas = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00'];
            foreach ($horas as $hora) {
                $start = Carbon::today()->setTime((int)substr($hora, 0, 2), 0);
                $end = $start->copy()->addHours(2);

                $labels[] = $hora;
                $keys[] = $hora;

                $realizadas[] = Appointment::where('status', 'COMPLETADA')
                    ->whereBetween('fecha_hora', [$start, $end])
                    ->count();

                $pendientes[] = Appointment::whereIn('status', ['PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO'])
                    ->whereBetween('fecha_hora', [$start, $end])
                    ->count();
            }
        } elseif ($this->filtroAtenciones === 'mes') {
            // Agrupar por semanas de este mes (S1 a S5)
            $semanas = [
                ['key' => 'dashboard.week1', 'fallback' => 'Semana 1'],
                ['key' => 'dashboard.week2', 'fallback' => 'Semana 2'],
                ['key' => 'dashboard.week3', 'fallback' => 'Semana 3'],
                ['key' => 'dashboard.week4', 'fallback' => 'Semana 4'],
                ['key' => 'dashboard.week5', 'fallback' => 'Semana 5'],
            ];

            for ($i = 1; $i <= 5; $i++) {
                $start = Carbon::today()->startOfMonth()->addWeeks($i - 1);
                $end = $start->copy()->endOfWeek();
                if ($end->isAfter(Carbon::today()->endOfMonth())) {
                    $end = Carbon::today()->endOfMonth();
                }

                $labels[] = $semanas[$i - 1]['fallback'];
                $keys[] = $semanas[$i - 1]['key'];

                $realizadas[] = Appointment::where('status', 'COMPLETADA')
                    ->whereBetween('fecha_hora', [$start, $end])
                    ->count();

                $pendientes[] = Appointment::whereIn('status', ['PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO'])
                    ->whereBetween('fecha_hora', [$start, $end])
                    ->count();
            }
        } elseif ($this->filtroAtenciones === 'anio') {
            $meses = [
                ['key' => 'dashboard.jan', 'fallback' => 'Ene'],
                ['key' => 'dashboard.feb', 'fallback' => 'Feb'],
                ['key' => 'dashboard.mar', 'fallback' => 'Mar'],
                ['key' => 'dashboard.apr', 'fallback' => 'Abr'],
                ['key' => 'dashboard.may', 'fallback' => 'May'],
                ['key' => 'dashboard.jun', 'fallback' => 'Jun'],
                ['key' => 'dashboard.jul', 'fallback' => 'Jul'],
                ['key' => 'dashboard.aug', 'fallback' => 'Ago'],
                ['key' => 'dashboard.sep', 'fallback' => 'Sep'],
                ['key' => 'dashboard.oct', 'fallback' => 'Oct'],
                ['key' => 'dashboard.nov', 'fallback' => 'Nov'],
                ['key' => 'dashboard.dec', 'fallback' => 'Dic'],
            ];

            for ($i = 1; $i <= 12; $i++) {
                $start = Carbon::today()->startOfYear()->addMonths($i - 1);
                $end = $start->copy()->endOfMonth();

                $labels[] = $meses[$i - 1]['fallback'];
                $keys[] = $meses[$i - 1]['key'];

                $realizadas[] = Appointment::where('status', 'COMPLETADA')
                    ->whereBetween('fecha_hora', [$start, $end])
                    ->count();

                $pendientes[] = Appointment::whereIn('status', ['PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO'])
                    ->whereBetween('fecha_hora', [$start, $end])
                    ->count();
            }
        } else {
            // Por defecto: 'semana' (últimos 7 días)
            $diasSemanaKeys = [
                0 => 'dashboard.sun',
                1 => 'dashboard.mon',
                2 => 'dashboard.tue',
                3 => 'dashboard.wed',
                4 => 'dashboard.thu',
                5 => 'dashboard.fri',
                6 => 'dashboard.sat',
            ];

            for ($i = 6; $i >= 0; $i--) {
                $dia = Carbon::today()->subDays($i);
                $labels[] = $dia->translatedFormat('D') . ' ' . $dia->format('d/m');
                $keys[] = $diasSemanaKeys[$dia->dayOfWeek] ?? 'dashboard.day';

                $realizadas[] = Appointment::where('status', 'COMPLETADA')
                    ->whereDate('fecha_hora', $dia)
                    ->count();

                $pendientes[] = Appointment::whereIn('status', ['PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO'])
                    ->whereDate('fecha_hora', $dia)
                    ->count();
            }
        }

        return [
            'labels' => $labels,
            'keys' => $keys,
            'realizadas' => $realizadas,
            'pendientes' => $pendientes,
        ];
    }

    private function obtenerEnfermedadesTop(): array
    {
        // 1. Obtener top 5 enfermedades
        $topEnfermedades = DB::table('medical_records')
            ->select('diagnostico_presuntivo', DB::raw('count(*) as total'))
            ->whereNotNull('diagnostico_presuntivo')
            ->where('diagnostico_presuntivo', '!=', '')
            ->groupBy('diagnostico_presuntivo')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $enfermedades = [];
        
        // Mapeo predictivo realista de medicamentos e insumos según enfermedad
        $medicinaSugerida = [
            'Dermatitis alérgica por pulgas' => [
                'recs' => 'Fipronil Pipeta, Shampoo Medicado con Clorhexidina',
                'equipo' => 'Microscopio para citología cutánea',
                'pred_factor' => 1.25, // Incremento por calor/verano
            ],
            'Gastroenteritis aguda' => [
                'recs' => 'Amoxicilina 500mg, Meloxicam Gotas, Ringer Lactato',
                'equipo' => 'Test rápido de Parvovirus, Ecógrafo',
                'pred_factor' => 1.05,
            ],
            'Otitis externa bilateral' => [
                'recs' => 'Gotas Otomax, Limpiador OtiClean',
                'equipo' => 'Otoscopio Digital',
                'pred_factor' => 1.10,
            ],
            'Tos de las perreras (traqueobronquitis)' => [
                'recs' => 'Cefalexina 250mg, Jarabes expectorantes, Vacuna KC',
                'equipo' => 'Estetoscopio de alta resolución, Nebulizador',
                'pred_factor' => 1.30, // Alto incremento en invierno
            ],
            'Rinotraqueitis felina' => [
                'recs' => 'Vacuna Triple Felina, Doxiciclina 100mg',
                'equipo' => 'Cámara de nebulización felina',
                'pred_factor' => 1.20,
            ],
            'Ehrlichiosis canina' => [
                'recs' => 'Doxiciclina 100mg, Hemolitan Gold',
                'equipo' => 'Test rápido 4Dx',
                'pred_factor' => 1.15,
            ],
            'Paciente sano - control preventivo' => [
                'recs' => 'Vacuna Séxtuple, Praziquantel + Pirantel',
                'equipo' => 'Balanza digital, Lector de Microchip',
                'pred_factor' => 1.00,
            ]
        ];

        foreach ($topEnfermedades as $enfermedad) {
            $nombre = $enfermedad->diagnostico_presuntivo;
            $total = $enfermedad->total;

            // Simular tendencia en base a los últimos 30 días
            $casosUltimoMes = DB::table('medical_records')
                ->where('diagnostico_presuntivo', $nombre)
                ->where('date', '>=', Carbon::now()->subDays(30))
                ->count();

            $promedioMensual = round($total / 12, 1);
            $tendencia = $casosUltimoMes > $promedioMensual ? 'ALZA' : ($casosUltimoMes < $promedioMensual ? 'BAJA' : 'ESTABLE');
            
            // Predicción inteligente
            $sug = $medicinaSugerida[$nombre] ?? [
                'recs' => 'Amoxicilina 500mg, analgésicos genéricos',
                'equipo' => 'Kits de diagnóstico rápido',
                'pred_factor' => 1.05
            ];

            $proyeccionSiguienteMes = max(1, round(($casosUltimoMes ?: $promedioMensual) * $sug['pred_factor']));

            $enfermedades[] = [
                'nombre' => $nombre,
                'total' => $total,
                'casos_mes' => $casosUltimoMes,
                'tendencia' => $tendencia,
                'proyeccion' => $proyeccionSiguienteMes,
                'medicamentos' => $sug['recs'],
                'equipamiento' => $sug['equipo'],
            ];
        }

        return $enfermedades;
    }

    private function obtenerSintomasTop(): array
    {
        $topSintomas = DB::table('medical_records')
            ->select('reason', DB::raw('count(*) as total'))
            ->whereNotNull('reason')
            ->where('reason', '!=', '')
            ->groupBy('reason')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $sintomas = [];

        // Mapeo de síntomas a equipamiento y pruebas sugeridas
        $sintomaRelacion = [
            'Prurito excesivo y enrojecimiento en la piel' => 'Kit de raspado cutáneo, lámpara de Wood, champús de tratamiento',
            'Pérdida de pelaje y picazón constante' => 'Citología cutánea, pruebas de descarte de ácaros/hongos',
            'Sacudidas frecuentes de cabeza y mal olor en oídos' => 'Otoscopio clínico, hisopos estériles para cultivo de secreciones',
            'Vómitos frecuentes y decaimiento' => 'Ecógrafo abdominal, test rápido de Parvovirus/Coronavirus',
            'Tos seca persistente y secreción nasal' => 'Radiografías torácicas, estetoscopio, cámara de nebulización',
            'Chequeo preventivo general y desparasitación' => 'Balanza digital, lector de microchips, antiparasitarios internos/externos',
            'Estornudos constantes y secreción ocular abundante' => 'Prueba PCR respiratoria felina, colirios antibióticos',
            'Fiebre alta, inapetencia y encías pálidas' => 'Analizador hematológico para hemograma, test rápido de Ehrlichia',
            'Orina con frecuencia y en poca cantidad' => 'Tiras reactivas de orina, analizador de sedimento urinario, ecógrafo',
        ];

        foreach ($topSintomas as $sintoma) {
            $sintomas[] = [
                'nombre' => $sintoma->reason,
                'total' => $sintoma->total,
                'insumos' => $sintomaRelacion[$sintoma->reason] ?? 'Kits de diagnóstico general, guantes, jeringas y termómetro',
            ];
        }

        return $sintomas;
    }

    private function obtenerIngresosGrafico($fechaInicio, $hoy): \Illuminate\Support\Collection
    {
        $ingresosGrafico = collect();
        if ($this->filtroTiempo === 'hoy') {
            $ventasAgrupadas = Sale::where('status', 'PAGADO')
                ->whereDate('created_at', $hoy)
                ->selectRaw('DATE_FORMAT(created_at, "%H:00") as fecha, SUM(total) as suma')
                ->groupBy('fecha')
                ->pluck('suma', 'fecha');

            for ($i = 8; $i <= 20; $i++) {
                $horaStr = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $total = $ventasAgrupadas->get($horaStr, 0);
                $ingresosGrafico->push([
                    'dia'   => str_pad($i, 2, '0', STR_PAD_LEFT) . 'h',
                    'key'   => str_pad($i, 2, '0', STR_PAD_LEFT) . 'h',
                    'date'  => $horaStr,
                    'total' => (float) $total,
                ]);
            }
        } elseif ($this->filtroTiempo === 'anio') {
            $diasAtras = Carbon::today()->startOfYear();
            $ventasAgrupadas = Sale::where('status', 'PAGADO')
                ->where('created_at', '>=', $diasAtras)
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as fecha, SUM(total) as suma')
                ->groupBy('fecha')
                ->pluck('suma', 'fecha');

            $mesesKeys = [
                1 => 'dashboard.jan', 2 => 'dashboard.feb', 3 => 'dashboard.mar',
                4 => 'dashboard.apr', 5 => 'dashboard.may', 6 => 'dashboard.jun',
                7 => 'dashboard.jul', 8 => 'dashboard.aug', 9 => 'dashboard.sep',
                10 => 'dashboard.oct', 11 => 'dashboard.nov', 12 => 'dashboard.dec',
            ];
            
            for ($i = 1; $i <= 12; $i++) {
                $mes = Carbon::today()->startOfYear()->addMonths($i - 1);
                $fechaStr = $mes->format('Y-m');
                $total = $ventasAgrupadas->get($fechaStr, 0);

                $ingresosGrafico->push([
                    'dia'   => $mes->translatedFormat('M'),
                    'key'   => $mesesKeys[$i] ?? 'dashboard.month',
                    'date'  => $mes->format('m/Y'),
                    'total' => (float) $total,
                ]);
            }
        } elseif ($this->filtroTiempo === 'mes') {
            $diasAtras = Carbon::today()->startOfMonth();
            $diasIterar = Carbon::today()->daysInMonth;
            
            $ventasAgrupadas = Sale::where('status', 'PAGADO')
                ->where('created_at', '>=', $diasAtras)
                ->where('created_at', '<=', Carbon::today()->endOfMonth()->endOfDay())
                ->selectRaw('DATE(created_at) as fecha, SUM(total) as suma')
                ->groupBy('fecha')
                ->pluck('suma', 'fecha');

            for ($i = 1; $i <= $diasIterar; $i++) {
                $dia = Carbon::today()->startOfMonth()->addDays($i - 1);
                $fechaStr = $dia->toDateString();
                $total = $ventasAgrupadas->get($fechaStr, 0);

                $ingresosGrafico->push([
                    'dia'   => $dia->format('d/m'),
                    'key'   => $dia->format('d/m'),
                    'date'  => $dia->format('d/m'),
                    'total' => (float) $total,
                ]);
            }
        } else {
            // Semana (por defecto últimos 7 días)
            $diasSemanaKeys = [
                0 => 'dashboard.sun',
                1 => 'dashboard.mon',
                2 => 'dashboard.tue',
                3 => 'dashboard.wed',
                4 => 'dashboard.thu',
                5 => 'dashboard.fri',
                6 => 'dashboard.sat',
            ];

            $diasAtras = Carbon::today()->subDays(6);
            $ventasAgrupadas = Sale::where('status', 'PAGADO')
                ->where('created_at', '>=', $diasAtras)
                ->selectRaw('DATE(created_at) as fecha, SUM(total) as suma')
                ->groupBy('fecha')
                ->pluck('suma', 'fecha');

            for ($i = 6; $i >= 0; $i--) {
                $dia = Carbon::today()->subDays($i);
                $fechaStr = $dia->toDateString();
                $total = $ventasAgrupadas->get($fechaStr, 0);

                $ingresosGrafico->push([
                    'dia'   => $dia->translatedFormat('D') . ' ' . $dia->format('d/m'),
                    'key'   => $diasSemanaKeys[$dia->dayOfWeek] ?? 'dashboard.day',
                    'date'  => $dia->format('d/m'),
                    'total' => (float) $total,
                ]);
            }
        }
        return $ingresosGrafico;
    }
}
