<?php
$data = json_decode(file_get_contents('public/locales/en.json'));
if ($data === null) {
    echo "Error decoding JSON: " . json_last_error_msg() . "\n";
} else {
    file_put_contents('public/locales/en.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "Fixed en.json\n";
}
