import glob
import re

files = glob.glob(r'c:\xampp\htdocs\vetcoressen\resources\views\pdf\*.blade.php')

for path in files:
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    php_snippet = '''@php
    $clinic = \App\Models\Clinic::first();
    $logoPath = $clinic && $clinic->logo ? public_path('storage/' . $clinic->logo) : public_path('favicon.svg');
    $logoSrc = '';
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoMime = mime_content_type($logoPath);
        $logoSrc = 'data:' . $logoMime . ';base64,' . $logoData;
    }
@endphp
'''

    if '@php' not in content[:200]:
        content = content.replace('<head>', '<head>\n' + php_snippet)

    content = re.sub(r'<h1>\{\{\s*config\(\'app\.name\', \'VETCORESSEN\'\)\s*\}\}</h1>', 
                     r'''@if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" style="max-height: 50px; margin-bottom: 5px;">
                @endif
                <h1 style="font-size: 20px;">{{ $clinic->name ?? config('app.name', 'VETCORESSEN') }}</h1>''', content)

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
print('Updated PDF logos.')
