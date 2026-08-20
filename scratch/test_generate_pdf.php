<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

$historia = \App\Models\MedicalRecord::with(['pet.cliente', 'veterinario', 'prescripciones.producto'])->first();
if ($historia) {
    $t = function($k, $d = null) { return $d ?? $k; };
    $pdf = Pdf::loadView('pdf.historia-clinica', compact('historia', 't'))->setPaper('a4', 'portrait');
    file_put_contents(__DIR__ . '/test_historia.pdf', $pdf->output());
    echo "test_historia.pdf generated successfully (" . strlen($pdf->output()) . " bytes)!\n";
} else {
    echo "No medical record found.\n";
}
