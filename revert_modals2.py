import os
import re
import subprocess

files = [
    "resources/views/livewire/proveedores/proveedor-index.blade.php",
    "resources/views/livewire/ajustes/usuarios-index.blade.php",
    "resources/views/livewire/ajustes/roles-index.blade.php"
]

for file in files:
    try:
        old_content = subprocess.check_output(['git', 'show', f'HEAD:{file}']).decode('utf-8')
    except Exception as e:
        print(f"Error reading {file} from git: {e}")
        continue
    
    try:
        with open(file, 'r', encoding='utf-8') as f:
            new_content = f.read()
    except Exception as e:
        print(f"Error reading {file}: {e}")
        continue

    if "proveedor-index" in file:
        modal_name = "confirmar-eliminacion"
    elif "usuarios-index" in file:
        modal_name = "confirmar-eliminar-usuario"
    elif "roles-index" in file:
        modal_name = "confirmar-eliminar-rol"
    
    old_match = re.search(r'(<flux:modal[^>]*name="' + modal_name + r'".*?</flux:modal>)', old_content, re.DOTALL)
    new_match = re.search(r'(<flux:modal[^>]*name="' + modal_name + r'".*?</flux:modal>)', new_content, re.DOTALL)

    if old_match and new_match:
        old_modal = old_match.group(1)
        new_modal = new_match.group(1)
        
        replaced = new_content.replace(new_modal, old_modal)
        with open(file, 'w', encoding='utf-8') as f:
            f.write(replaced)
        print(f"Reverted modal in {file}")
    else:
        print(f"Could not find modal in {file}. Old found: {bool(old_match)}, New found: {bool(new_match)}")
