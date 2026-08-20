<?php
$content = file_get_contents('resources/views/livewire/citas/cita-index.blade.php');
$lines = explode("\n", $content);
$stack = [];
foreach ($lines as $num => $line) {
    if (preg_match('/@(if|unless|isset|empty|auth|guest)\b(?!.*@(endif|endunless|endisset|endempty|endauth|endguest))/', $line, $m)) {
        $stack[] = ['type' => $m[1], 'line' => $num + 1, 'text' => trim($line)];
    } elseif (preg_match('/@(endif|endunless|endisset|endempty|endauth|endguest)\b/', $line, $m)) {
        $last = array_pop($stack);
        echo "Closed {$last['type']} from line {$last['line']} at line " . ($num + 1) . PHP_EOL;
    }
}
echo 'Unclosed directives count: ' . count($stack) . PHP_EOL;
print_r($stack);
