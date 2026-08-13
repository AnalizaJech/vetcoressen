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
    
    public function updatedPeriodo()
    {
        $data = $this->getReportData();

        $this->dispatch('charts-updated', [
            'ventasLabels' => $data['ventasChartLabels'],
            'ventasData' => $data['ventasChartData'],
            'citasLabels' => $data['citasChartLabels'],
            'citasData' => $data['citasChartData']
        ]);
    }

    private function getReportData()
    {
        $now = Carbon::now();
        $startDate = clone $now;
        $prevStartDate = clone $now;
        $prevEndDate = clone $now;
        $endDate = clone $now;

        if ($this->periodo === 'hoy') {
            $startDate->startOfDay();
            $prevStartDate->subDay()->startOfDay();
            $prevEndDate->subDay()->endOfDay();
        } elseif ($this->periodo === 'semana_actual') {
            $startDate->startOfWeek();
            $prevStartDate->subWeek()->startOfWeek();
            $prevEndDate->subWeek()->endOfWeek();
        } elseif ($this->periodo === 'anio_actual') {
            $startDate->startOfYear();
            $prevStartDate->subYear()->startOfYear();
            $prevEndDate->subYear()->endOfYear();
        } else {
            // mes_actual
            $startDate->startOfMonth();
            $prevStartDate->subMonth()->startOfMonth();
            $prevEndDate->subMonth()->endOfMonth();
        }

        $sales = Sale::with(['cliente', 'detalles.producto'])->where('status', 'PAGADO')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
            
        $prevSales = Sale::where('status', 'PAGADO')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->get();

        $ventasPeriodo = $sales->sum('total');
        $prevVentasPeriodo = $prevSales->sum('total');
        $porcentajeVentas = $prevVentasPeriodo > 0 ? (($ventasPeriodo - $prevVentasPeriodo) / $prevVentasPeriodo) * 100 : ($ventasPeriodo > 0 ? 100 : 0);

        $ticketPromedio = $sales->count() > 0 ? $sales->avg('total') : 0;
        $prevTicketPromedio = $prevSales->count() > 0 ? $prevSales->avg('total') : 0;
        $porcentajeTicket = $prevTicketPromedio > 0 ? (($ticketPromedio - $prevTicketPromedio) / $prevTicketPromedio) * 100 : ($ticketPromedio > 0 ? 100 : 0);

        $ventasChartLabels = [];
        $ventasChartData = [];

        if ($this->periodo === 'hoy') {
            // From 00:00 to 23:00
            for ($i = 0; $i < 24; $i++) {
                $time = sprintf('%02d:00', $i);
                $ventasChartLabels[] = $time;
                $ventasChartData[$time] = 0;
            }
            foreach ($sales as $s) {
                $time = $s->created_at->format('H:00');
                $ventasChartData[$time] += $s->total;
            }
            $ventasChartData = array_values($ventasChartData);
        } elseif ($this->periodo === 'anio_actual') {
            // Jan to Dec
            for ($i = 1; $i <= 12; $i++) {
                $month = str_pad($i, 2, '0', STR_PAD_LEFT);
                $monthName = Carbon::createFromFormat('m', $month)->translatedFormat('M');
                $ventasChartLabels[] = ucfirst($monthName);
                $ventasChartData[$month] = 0;
            }
            foreach ($sales as $s) {
                $month = $s->created_at->format('m');
                $ventasChartData[$month] += $s->total;
            }
            $ventasChartData = array_values($ventasChartData);
        } elseif ($this->periodo === 'semana_actual') {
            // Monday to Sunday
            $start = clone $startDate;
            for ($i = 0; $i < 7; $i++) {
                $date = $start->copy()->addDays($i);
                $ventasChartLabels[] = $date->translatedFormat('D d/m');
                $ventasChartData[$date->format('Y-m-d')] = 0;
            }
            foreach ($sales as $s) {
                $date = $s->created_at->format('Y-m-d');
                if (isset($ventasChartData[$date])) {
                    $ventasChartData[$date] += $s->total;
                }
            }
            $ventasChartData = array_values($ventasChartData);
        } else {
            // mes_actual
            $start = clone $startDate;
            $end = clone $startDate;
            $end->endOfMonth();
            $daysInMonth = $end->day;
            
            for ($i = 0; $i < $daysInMonth; $i++) {
                $date = $start->copy()->addDays($i);
                $ventasChartLabels[] = $date->format('d/m');
                $ventasChartData[$date->format('Y-m-d')] = 0;
            }
            foreach ($sales as $s) {
                $date = $s->created_at->format('Y-m-d');
                if (isset($ventasChartData[$date])) {
                    $ventasChartData[$date] += $s->total;
                }
            }
            $ventasChartData = array_values($ventasChartData);
        }

        $appointments = Appointment::with(['cliente', 'mascota'])->whereBetween('fecha_hora', [$startDate, $endDate])->get();
        $prevAppointments = Appointment::whereBetween('fecha_hora', [$prevStartDate, $prevEndDate])->get();
        
        $citasNuevas = Appointment::whereBetween('created_at', [$startDate, $endDate])->count();
        $prevCitasNuevas = Appointment::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $porcentajeNuevas = $prevCitasNuevas > 0 ? (($citasNuevas - $prevCitasNuevas) / $prevCitasNuevas) * 100 : ($citasNuevas > 0 ? 100 : 0);

        $citasCompletadas = $appointments->where('status', 'COMPLETADA')->count();
        $prevCitasCompletadas = $prevAppointments->where('status', 'COMPLETADA')->count();
        $porcentajeCitas = $prevCitasCompletadas > 0 ? (($citasCompletadas - $prevCitasCompletadas) / $prevCitasCompletadas) * 100 : ($citasCompletadas > 0 ? 100 : 0);
        
        $citasCanceladas = $appointments->where('status', 'CANCELADA')->count();
        $citasOtras = $appointments->count() - $citasCompletadas - $citasCanceladas;

        $citasChartLabels = ['Completadas', 'Canceladas', 'Pendientes/Otras'];
        $citasChartData = [$citasCompletadas, $citasCanceladas, $citasOtras];

        $productosStockBajo = Product::where('type', '!=', 'SERVICIO')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count();

        return compact(
            'ventasPeriodo', 'porcentajeVentas',
            'ticketPromedio', 'porcentajeTicket', 
            'sales',
            'citasNuevas', 'porcentajeNuevas',
            'citasCompletadas', 'porcentajeCitas',
            'citasCanceladas', 'appointments',
            'productosStockBajo',
            'ventasChartLabels', 'ventasChartData',
            'citasChartLabels', 'citasChartData'
        );
    }

    public function exportarPdf()
    {
        $data = $this->getReportData();
        $data['periodo'] = $this->periodo;
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_' . $this->periodo . '.pdf');
    }

    public function exportarCsv()
    {
        $data = $this->getReportData();
        $csv = "REPORTE ESTADISTICO - " . strtoupper(str_replace('_', ' ', $this->periodo)) . "\n\n";
        
        // Ventas Details
        $csv .= "--- DETALLE DE VENTAS ---\n";
        $csv .= "ID Venta,Fecha,Cliente,DNI/RUC,Comprobante,Metodo Pago,Estado Venta,Producto/Servicio,Tipo Producto,Cantidad,Precio Unitario,Subtotal,Descuento,Total Venta\n";
        foreach($data['sales'] as $sale) {
            $clienteNombre = $sale->cliente ? str_replace(',', ' ', $sale->cliente->nombre_completo) : 'Cliente General';
            $clienteDoc = $sale->cliente ? $sale->cliente->numero_documento : '-';
            $fecha = $sale->created_at->format('Y-m-d H:i');
            $estado = $sale->status;
            
            if ($sale->detalles && $sale->detalles->count() > 0) {
                foreach($sale->detalles as $detalle) {
                    $prodNombre = $detalle->producto ? str_replace(',', ' ', $detalle->producto->name) : 'Producto Genérico';
                    $prodTipo = $detalle->producto ? $detalle->producto->type : '-';
                    $csv .= "{$sale->id},{$fecha},{$clienteNombre},{$clienteDoc},{$sale->tipo_comprobante},{$sale->payment_method},{$estado},{$prodNombre},{$prodTipo},{$detalle->quantity},{$detalle->unit_price},{$detalle->subtotal},{$sale->discount},{$sale->total}\n";
                }
            } else {
                $csv .= "{$sale->id},{$fecha},{$clienteNombre},{$clienteDoc},{$sale->tipo_comprobante},{$sale->payment_method},{$estado},Sin Detalles,-,1,{$sale->total},{$sale->total},{$sale->discount},{$sale->total}\n";
            }
        }
        $csv .= "\n";
        
        // Citas Details
        $csv .= "--- DETALLE DE CITAS ---\n";
        $csv .= "ID Cita,Fecha Programada,Cliente,Mascota,Especie,Motivo,Estado,Veterinario\n";
        foreach($data['appointments'] as $appt) {
            $clienteNombre = $appt->cliente ? str_replace(',', ' ', $appt->cliente->nombre_completo) : '-';
            $mascotaNombre = $appt->mascota ? str_replace(',', ' ', $appt->mascota->name) : '-';
            $especie = $appt->mascota ? ($appt->mascota->especie->name ?? '-') : '-';
            $fecha = $appt->fecha_hora ? $appt->fecha_hora->format('Y-m-d H:i') : '-';
            $motivo = str_replace(',', ' ', $appt->reason ?? '-');
            $veterinario = $appt->veterinario ? str_replace(',', ' ', $appt->veterinario->name) : '-';
            $csv .= "{$appt->id},{$fecha},{$clienteNombre},{$mascotaNombre},{$especie},{$motivo},{$appt->status},{$veterinario}\n";
        }
        $csv .= "\n";

        // Summary
        $csv .= "--- RESUMEN METRICAS ---\n";
        $csv .= "METRICA,VALOR\n";
        $csv .= "Ingresos del Periodo," . $data['ventasPeriodo'] . "\n";
        $csv .= "Ticket Promedio," . $data['ticketPromedio'] . "\n";
        $csv .= "Citas Completadas," . $data['citasCompletadas'] . "\n";
        $csv .= "Citas Canceladas," . $data['citasCanceladas'] . "\n";
        $csv .= "Nuevas Reservas," . $data['citasNuevas'] . "\n";
        $csv .= "Productos con Stock Bajo," . $data['productosStockBajo'] . "\n";

        return response()->streamDownload(function () use ($csv) {
            echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
            echo $csv;
        }, 'reporte_detallado_' . $this->periodo . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render()
    {
        $data = $this->getReportData();

        return view('livewire.reportes.reporte-index', $data);
    }
}
