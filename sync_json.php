<?php
$esPath = __DIR__ . '/public/locales/es.json';
$enPath = __DIR__ . '/public/locales/en.json';

$es = json_decode(file_get_contents($esPath), true) ?? [];
$en = json_decode(file_get_contents($enPath), true) ?? [];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
$regex = '/\$store\.i18n\.t\(([\'"])([^\'"]+)\1\)/';

$keysFound = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match_all($regex, $content, $matches)) {
            foreach ($matches[2] as $key) {
                $keysFound[$key] = true;
            }
        }
    }
}

// Add specifically mentioned missing keys
$keysFound['form.select'] = true;
$keysFound['form.selectClient'] = true;
$keysFound['placeholder.petName'] = true;
$keysFound['placeholder.weight'] = true;
$keysFound['placeholder.notes'] = true;

// Deep merge nested keys
function setNestedValue(&$array, $key, $value) {
    $keys = explode('.', $key);
    $current = &$array;
    foreach ($keys as $i => $k) {
        if ($i === count($keys) - 1) {
            if (!isset($current[$k])) {
                $current[$k] = str_replace('_', ' ', ucfirst($k));
            }
        } else {
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
    }
}

foreach ($keysFound as $key => $true) {
    setNestedValue($es, $key, $key);
    setNestedValue($en, $key, $key);
}

// Additional specific manual values for ES
$manualEs = [
    'form' => [
        'select' => 'Seleccionar',
        'selectClient' => 'Seleccionar Cliente',
        'petName' => 'Nombre de la Mascota',
        'weight' => 'Peso (kg)',
        'notes' => 'Notas médicas adicionales...',
        'birthDate' => 'Fecha de Nacimiento'
    ],
    'placeholder' => [
        'petName' => 'Nombre de la Mascota',
        'weight' => 'Peso (kg)',
        'notes' => 'Notas médicas adicionales...'
    ],
    'title' => [
        'roles_y_permisos' => 'Roles y Permisos',
        'sucursales' => 'Sucursales',
        'proveedores' => 'Proveedores',
        'usuarios' => 'Usuarios'
    ]
];

// Merge manual ES
foreach ($manualEs as $group => $items) {
    if (!isset($es[$group])) $es[$group] = [];
    foreach ($items as $k => $v) {
        $es[$group][$k] = $v;
    }
}

file_put_contents($esPath, json_encode($es, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents($enPath, json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "JSON synced successfully. Found " . count($keysFound) . " unique keys.\n";
