<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function getT($lang) {
    $json = json_decode(file_get_contents("public/locales/{$lang}.json"), true);
    return function($key, $default = null) use ($json) {
        $keys = explode('.', $key);
        $curr = $json;
        foreach ($keys as $k) {
            if (!isset($curr[$k])) return $default ?? $key;
            $curr = $curr[$k];
        }
        return is_string($curr) ? $curr : ($default ?? $key);
    };
}

$historia = \App\Models\MedicalRecord::with(['pet.cliente', 'veterinario', 'prescripciones.producto'])->first();

$tEn = getT('en');
$tEs = getT('es');

$pdfEn = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.historia-clinica', ['historia' => $historia, 't' => $tEn])->setPaper('a4', 'portrait');
$pdfEs = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.historia-clinica', ['historia' => $historia, 't' => $tEs])->setPaper('a4', 'portrait');

file_put_contents('scratch/historia_en.pdf', $pdfEn->output());
file_put_contents('scratch/historia_es.pdf', $pdfEs->output());

echo "EN PDF generated: " . strlen($pdfEn->output()) . " bytes\n";
echo "ES PDF generated: " . strlen($pdfEs->output()) . " bytes\n";
