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
                # Replace searchable="true" or searchable
                new_content = re.sub(r'(\s+searchable=[\'\"]true[\'\"]|\s+searchable)', '', content)
                if new_content != content:
                    with open(filepath, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    print(f'Removed searchable from {filepath}')
