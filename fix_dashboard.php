<?php
$file = __DIR__ . '/resources/views/livewire/dashboard.blade.php';
$content = file_get_contents($file);

$replacements = [
    'Ingresos sltima Semana' => 'Ingresos Última Semana',
    'Ingresos Este Ao' => 'Ingresos Este Año',
    'Aǧn no hay ventas' => 'Aún no hay ventas',
    'este ao' => 'este año',
    'Grǭfico' => 'Gráfico'
];

foreach ($replacements as $search => $replace) {
    // We will do a generic regex replace for bad characters since they might be corrupted
}

// Easier to just use regex
$content = preg_replace('/Ingresos .*?sltima Semana/', 'Ingresos Última Semana', $content);
$content = preg_replace('/Ingresos Este A.*?o/', 'Ingresos Este Año', $content);
$content = preg_replace('/A.*?n no hay ventas/', 'Aún no hay ventas', $content);
$content = preg_replace('/este a.*?o/', 'este año', $content);
$content = preg_replace('/Gr.*?fico/', 'Gráfico', $content);

file_put_contents($file, $content);
echo "Done dashboard\n";
