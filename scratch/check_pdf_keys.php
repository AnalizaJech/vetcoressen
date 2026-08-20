<?php

$files = [
    'resources/views/pdf/historia-clinica.blade.php',
    'resources/views/pdf/cita.blade.php',
    'resources/views/pdf/historial-mascota.blade.php',
    'resources/views/pdf/reporte.blade.php',
];

$es = json_decode(file_get_contents('public/locales/es.json'), true);
$en = json_decode(file_get_contents('public/locales/en.json'), true);

function getNested($arr, $key) {
    $parts = explode('.', $key);
    $curr = $arr;
    foreach ($parts as $p) {
        if (!isset($curr[$p])) return null;
        $curr = $curr[$p];
    }
    return $curr;
}

$missing = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    preg_match_all('/\$t\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
    foreach ($matches[1] as $key) {
        if (getNested($es, $key) === null) {
            $missing['es'][$key] = true;
        }
        if (getNested($en, $key) === null) {
            $missing['en'][$key] = true;
        }
    }
}

echo "Missing in ES: " . count($missing['es'] ?? []) . "\n";
print_r(array_keys($missing['es'] ?? []));
echo "Missing in EN: " . count($missing['en'] ?? []) . "\n";
print_r(array_keys($missing['en'] ?? []));
