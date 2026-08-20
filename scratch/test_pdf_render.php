<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$historia = \App\Models\MedicalRecord::with(['pet.cliente', 'veterinario', 'prescripciones.producto'])->first();
if (!$historia) {
    echo "No medical record found\n";
    exit(1);
}

$t = function($key, $default = null) {
    return $default ?? $key;
};

$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.historia-clinica', compact('historia', 't'))
    ->setPaper('a4', 'portrait');

$output = $pdf->output();
file_put_contents('scratch/test_output.pdf', $output);
echo "PDF successfully generated, size: " . strlen($output) . " bytes\n";
