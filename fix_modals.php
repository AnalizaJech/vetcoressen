<?php
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
$count = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Find <flux:button variant="ghost" ...> and replace with <button type="button" class="btn-secondary ...">
        $newContent = preg_replace_callback(
            '/<flux:button\s+variant="ghost"(.*?)>(.*?)<\/flux:button>/s',
            function($matches) {
                // Ensure w-full is in the class
                $attrs = $matches[1];
                if (strpos($attrs, 'class="') !== false) {
                    $attrs = preg_replace('/class="/', 'class="btn-secondary justify-center ', $attrs);
                } else {
                    $attrs .= ' class="btn-secondary justify-center w-full sm:w-auto"';
                }
                return '<button type="button"' . $attrs . '>' . $matches[2] . '</button>';
            },
            $content
        );

        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated: " . $file->getFilename() . "\n";
            $count++;
        }
    }
}
echo "Done! Updated $count files.\n";
