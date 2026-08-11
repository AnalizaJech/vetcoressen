import os
import re

regex = re.compile(r'x-bind:placeholder="\$store\.i18n\.t\(\'([^\']+)\'\)[^"]+"')

count = 0
for r, d, files in os.walk(r'c:\xampp\htdocs\vetcoressen\resources\views'):
    for f in files:
        if f.endswith('.blade.php'):
            path = os.path.join(r, f)
            with open(path, 'r', encoding='utf8') as file:
                content = file.read()
            
            if 'x-bind:placeholder' in content:
                new_content = regex.sub(r'placeholder="\1"', content)
                
                # Also handle double quote case inside the match if needed, but our regex expects single quotes inside t('')
                if new_content != content:
                    with open(path, 'w', encoding='utf8') as file:
                        file.write(new_content)
                    print('Fixed in', path)
                    count += 1
print(f'Fixed {count} files.')
