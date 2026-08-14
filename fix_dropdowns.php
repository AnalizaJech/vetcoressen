<?php
$files = [
    'resources/views/livewire/mascotas/mascota-index.blade.php' => [
        ['placeholder="Todos los clientes"', 'placeholder="filter.allClients"' . "\n" . '                :selected="$filtroCliente"'],
        ['placeholder="Todas las mascotas"', 'placeholder="filter.allPets"' . "\n" . '                :selected="$filtroMascota"']
    ],
    'resources/views/livewire/proveedores/proveedor-index.blade.php' => [
        ['placeholder="Todos los proveedores"', 'placeholder="filter.allSuppliers"' . "\n" . '                    :selected="$filtroProveedor"']
    ],
    'resources/views/livewire/sucursales/sucursal-index.blade.php' => [
        ['placeholder="Todas las sucursales"', 'placeholder="filter.allBranches"' . "\n" . '                    :selected="$filtroSucursal"']
    ]
];

foreach ($files as $file => $replacements) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        foreach ($replacements as $repl) {
            $content = str_replace($repl[0], $repl[1], $content);
        }
        file_put_contents($path, $content);
    }
}
echo "Dropdowns updated!\n";
