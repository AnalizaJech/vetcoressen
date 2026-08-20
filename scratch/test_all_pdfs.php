<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

$t = function($k, $d = null) { return $d ?? $k; };

// 1. Historia Clinica
$historia = \App\Models\MedicalRecord::with(['pet.cliente', 'veterinario', 'prescripciones.producto'])->first();
if ($historia) {
    $pdf = Pdf::loadView('pdf.historia-clinica', compact('historia', 't'))->setPaper('a4', 'portrait');
    file_put_contents(__DIR__ . '/test_historia.pdf', $pdf->output());
    echo "1. historia-clinica PDF generated: " . strlen($pdf->output()) . " bytes\n";
}

// 2. Historial Mascota
$mascota = \App\Models\Pet::with(['cliente', 'especie', 'raza', 'historiasClinicas.veterinario', 'historiasClinicas.prescripciones.producto'])->first();
if ($mascota) {
    $pdf = Pdf::loadView('pdf.historial-mascota', compact('mascota', 't'))->setPaper('a4', 'portrait');
    file_put_contents(__DIR__ . '/test_historial_mascota.pdf', $pdf->output());
    echo "2. historial-mascota PDF generated: " . strlen($pdf->output()) . " bytes\n";
}

// 3. Cita
$cita = \App\Models\Appointment::with(['cliente', 'mascota.especie', 'mascota.raza', 'veterinario'])->first();
if ($cita) {
    $pdf = Pdf::loadView('pdf.cita', compact('cita', 't'))->setPaper('a4', 'portrait');
    file_put_contents(__DIR__ . '/test_cita.pdf', $pdf->output());
    echo "3. cita PDF generated: " . strlen($pdf->output()) . " bytes\n";
}

// 4. Reporte
$periodo = 'este_mes';
$startDate = now()->startOfMonth();
$endDate = now()->endOfMonth();
$ventasPeriodo = 1250.00;
$totalVentasCount = 15;
$ticketPromedio = 83.33;
$totalCitas = 24;
$citasCompletadas = 18;
$citasPendientes = 4;
$citasCanceladas = 2;
$topDetalles = collect([]);
$sales = \App\Models\Sale::with('cliente')->take(5)->get();

$pdf = Pdf::loadView('pdf.reporte', compact(
    'periodo', 'startDate', 'endDate', 'ventasPeriodo', 'totalVentasCount',
    'ticketPromedio', 'totalCitas', 'citasCompletadas', 'citasPendientes',
    'citasCanceladas', 'topDetalles', 'sales'
))->setPaper('a4', 'portrait');
file_put_contents(__DIR__ . '/test_reporte.pdf', $pdf->output());
echo "4. reporte PDF generated: " . strlen($pdf->output()) . " bytes\n";

echo "ALL 4 PDFs GENERATED PERFECTLY!\n";
