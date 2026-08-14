<?php
$file = __DIR__ . '/public/locales/es.json';
$data = json_decode(file_get_contents($file), true);

$data['title'] = [
    'dashboard' => 'Dashboard',
    'proveedores' => 'Proveedores',
    'formulario_de_proveedor' => 'Formulario de Proveedor',
    'roles_y_permisos' => 'Roles y Permisos',
    'sucursales' => 'Sucursales',
    'formulario_de_sucursal' => 'Formulario de Sucursal',
    'configuración' => 'Configuración',
    'nueva_venta' => 'Nueva Venta',
    'usuarios' => 'Usuarios',
    'usuario' => 'Usuario',
    'roles_y_permisos' => 'Roles y Permisos',
    'clientes' => 'Clientes',
    'cliente' => 'Cliente',
    'mascotas' => 'Mascotas',
    'mascota' => 'Mascota',
    'citas' => 'Citas',
    'cita' => 'Cita',
    'inventario' => 'Inventario',
    'producto' => 'Producto',
    'recepcionar_pedido' => 'Recepcionar Pedido',
    'historias_clínicas' => 'Historias Clínicas',
    'historia_clínica' => 'Historia Clínica',
    'caja' => 'Caja',
    'reportes_y_estadísticas' => 'Reportes y Estadísticas',
    'iniciar_sesión' => 'Iniciar Sesión',
];

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "es.json updated\n";
