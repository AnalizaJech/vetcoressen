import os
import re

directory = 'c:/Users/JORGE/Desktop/vetcoressen/resources/views/livewire'

for root, _, files in os.walk(directory):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            if 'searchable' in content and 'flux:select' in content:
                print(f'Processing {filepath}')
                new_content = re.sub(r'\s+searchable=(\"|\')true(\"|\')', '', content)
                new_content = re.sub(r'\s+searchable(?![\w\-\=])', '', new_content)
                if new_content != content:
                    with open(filepath, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    print(f'Removed searchable from {filepath}')
