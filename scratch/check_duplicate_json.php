<?php
function findDuplicateKeys($jsonStr) {
    $keys = [];
    $duplicates = [];
    $lines = explode("\n", $jsonStr);
    foreach ($lines as $lineNum => $line) {
        if (preg_match('/^\s*"([^"]+)"\s*:\s*\{/', $line, $m)) {
            $key = $m[1];
            if (isset($keys[$key])) {
                $duplicates[] = "$key (lines {$keys[$key]} and " . ($lineNum + 1) . ")";
            } else {
                $keys[$key] = $lineNum + 1;
            }
        }
    }
    return $duplicates;
}

$en = file_get_contents(__DIR__ . '/../public/locales/en.json');
$es = file_get_contents(__DIR__ . '/../public/locales/es.json');

echo "EN duplicates: " . implode(', ', findDuplicateKeys($en)) . "\n";
echo "ES duplicates: " . implode(', ', findDuplicateKeys($es)) . "\n";
