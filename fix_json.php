<?php
$enFile = 'public/locales/en.json';
$esFile = 'public/locales/es.json';

foreach ([$enFile, $esFile] as $file) {
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        
        // Find all keys that contain a dot and move them to nested arrays
        foreach ($data as $key => $val) {
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                if (count($parts) == 2) {
                    if (!isset($data[$parts[0]]) || !is_array($data[$parts[0]])) {
                        $data[$parts[0]] = [];
                    }
                    $data[$parts[0]][$parts[1]] = $val;
                }
                unset($data[$key]);
            }
        }
        
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
echo 'JSON files fixed.';
