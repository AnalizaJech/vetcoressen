<?php

$esJson = json_decode(file_get_contents('public/locales/es.json'), true);
$enJson = json_decode(file_get_contents('public/locales/en.json'), true);

function getNested($arr, $path) {
    $keys = explode('.', $path);
    $curr = $arr;
    foreach ($keys as $k) {
        if (!isset($curr[$k])) {
            return null;
        }
        $curr = $curr[$k];
    }
    return $curr;
}

$views = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
$foundKeys = [];

foreach ($views as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        preg_match_all("/\\\$store\.i18n\.t\(\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $k) {
                if (!str_contains($k, '$') && !str_contains($k, '+')) {
                    $foundKeys[$k][] = $file->getFilename();
                }
            }
        }
    }
}

$missingEn = [];
$missingEs = [];

foreach ($foundKeys as $k => $files) {
    if (getNested($enJson, $k) === null) {
        $missingEn[$k] = array_unique($files);
    }
    if (getNested($esJson, $k) === null) {
        $missingEs[$k] = array_unique($files);
    }
}

echo "=== MISSING IN EN.JSON (" . count($missingEn) . ") ===\n";
foreach ($missingEn as $k => $files) {
    echo "$k (in " . implode(', ', $files) . ")\n";
}

echo "\n=== MISSING IN ES.JSON (" . count($missingEs) . ") ===\n";
foreach ($missingEs as $k => $files) {
    echo "$k (in " . implode(', ', $files) . ")\n";
}
