<?php
$d = json_decode(file_get_contents('public/locales/es.json'), true);
if (!isset($d['filter'])) $d['filter'] = [];
$d['filter']['allClients'] = 'Todos los clientes';
$d['filter']['allPets'] = 'Todas las mascotas';
$d['filter']['allSuppliers'] = 'Todos los proveedores';
$d['filter']['allBranches'] = 'Todas las sucursales';
file_put_contents('public/locales/es.json', json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Done";
