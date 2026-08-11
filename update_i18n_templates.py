import re

files = [
    r'c:\xampp\htdocs\vetcoressen\resources\views\livewire\sucursales\sucursal-index.blade.php',
    r'c:\xampp\htdocs\vetcoressen\resources\views\livewire\proveedores\proveedor-index.blade.php'
]

for fpath in files:
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Simple replaces for Sucursales
    if 'sucursal' in fpath.lower():
        content = content.replace('Sucursales</x-slot:title>', '</x-slot:title>')
        content = content.replace('<x-slot:title>', '<x-slot:title x-text=\"$store.i18n.t(\'sidebar.branches\') || \'Sucursales\'\">Sucursales')
        content = content.replace('Gestión de sucursales de la clínica', '<span x-text=\"$store.i18n.t(\'sidebar.branches\') || \'Gestión de sucursales de la clínica\'\">Gestión de sucursales de la clínica</span>')
        content = content.replace('Nueva Sucursal', '<span x-text=\"$store.i18n.t(\'btn.newBranch\') || \'Nueva Sucursal\'\">Nueva Sucursal</span>')
        content = content.replace('Buscar sucursales...', '<span x-text=\"$store.i18n.t(\'placeholder.searchBranches\') || \'Buscar sucursales...\'\">Buscar sucursales...</span>')
    
    # Simple replaces for Proveedores
    if 'proveedor' in fpath.lower():
        content = content.replace('Proveedores</x-slot:title>', '</x-slot:title>')
        content = content.replace('<x-slot:title>', '<x-slot:title x-text=\"$store.i18n.t(\'sidebar.suppliers\') || \'Proveedores\'\">Proveedores')
        content = content.replace('Gestión de proveedores de la clínica', '<span x-text=\"$store.i18n.t(\'sidebar.suppliers\') || \'Gestión de proveedores de la clínica\'\">Gestión de proveedores de la clínica</span>')
        content = content.replace('Nuevo Proveedor', '<span x-text=\"$store.i18n.t(\'btn.newSupplier\') || \'Nuevo Proveedor\'\">Nuevo Proveedor</span>')
        content = content.replace('Buscar proveedores...', '<span x-text=\"$store.i18n.t(\'placeholder.searchSuppliers\') || \'Buscar proveedores...\'\">Buscar proveedores...</span>')
        
    with open(fpath, 'w', encoding='utf-8') as f:
        f.write(content)
print('Applied basic i18n to Sucursales and Proveedores.')
