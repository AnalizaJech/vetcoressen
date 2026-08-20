<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dash = new App\Livewire\Dashboard();
$dash->mount();

$periods = ['hoy', 'semana', 'mes', 'anio'];
foreach ($periods as $p) {
    $dash->filtroAtenciones = $p;
    $dash->updatedFiltroAtenciones();
    echo "Atenciones ($p): " . count($dash->atencionesGrafico['labels']) . " labels -> [" . implode(', ', $dash->atencionesGrafico['labels']) . "]\n";

    $dash->filtroTiempo = $p;
    $dash->updatedFiltroTiempo();
    echo "Ingresos ($p): " . count($dash->ingresosSemana) . " items\n";
}

echo "All period updates successful!\n";
