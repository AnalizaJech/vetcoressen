<?php

$es = json_decode(file_get_contents('public/locales/es.json'), true);
$en = json_decode(file_get_contents('public/locales/en.json'), true);

function setNested(&$arr, $path, $val) {
    $keys = explode('.', $path);
    $curr = &$arr;
    foreach ($keys as $k) {
        if (!isset($curr[$k]) || !is_array($curr[$k])) {
            $curr[$k] = [];
        }
        $curr = &$curr[$k];
    }
    $curr = $val;
}

$additions = [
    'page.salesList' => ['es' => 'Ver Ventas', 'en' => 'Sales List'],
    'btn.uncheckAll' => ['es' => 'Desmarcar Todos', 'en' => 'Uncheck All'],
    'btn.checkAll' => ['es' => 'Marcar Todos', 'en' => 'Check All'],
    'misc.outOfStock' => ['es' => 'Sin Stock', 'en' => 'Out of Stock'],
    'form.exact' => ['es' => 'Exacto', 'en' => 'Exact'],
    'form.change' => ['es' => 'Vuelto', 'en' => 'Change'],
    'modal.clearCartTitle' => ['es' => '¿Vaciar carrito?', 'en' => 'Clear cart?'],
    'modal.clearCartMsg' => ['es' => 'Se eliminarán todos los productos agregados a la venta actual.', 'en' => 'All products added to the current sale will be removed.'],
    'form.timePlaceholder' => ['es' => 'Seleccione hora', 'en' => 'Select time'],
    'filter.searchClient' => ['es' => 'Buscar Cliente', 'en' => 'Search Client'],
    'btn.newRecord' => ['es' => 'Nueva Historia', 'en' => 'New Medical Record'],
    'filter.pet' => ['es' => 'Mascota', 'en' => 'Pet'],
    'filter.species' => ['es' => 'Especie', 'en' => 'Species'],
    'form.male' => ['es' => 'Macho', 'en' => 'Male'],
    'form.female' => ['es' => 'Hembra', 'en' => 'Female'],
    'report.downloadHistoryPDF' => ['es' => 'Historial Completo (PDF)', 'en' => 'Complete History (PDF)'],
    'btn.viewRecord' => ['es' => 'Ver Ficha', 'en' => 'View Record'],
    'records.noPets' => ['es' => 'No hay mascotas registradas para este cliente.', 'en' => 'No pets registered for this client.'],
    'filter.searchProduct' => ['es' => 'Buscar Producto', 'en' => 'Search Product'],
    'filter.product' => ['es' => 'Producto', 'en' => 'Product'],
    'filter.category' => ['es' => 'Categoría', 'en' => 'Category'],
    'filter.stockStatus' => ['es' => 'Estado de Stock', 'en' => 'Stock Status'],
    'table.saleNumber' => ['es' => 'N° Venta', 'en' => 'Sale #'],
    
    // Additional helpful mappings for product types
    'misc.medication' => ['es' => 'Medicamento', 'en' => 'Medication'],
    'misc.food' => ['es' => 'Alimento', 'en' => 'Food'],
    'misc.accessory' => ['es' => 'Accesorio', 'en' => 'Accessory'],
    'misc.service' => ['es' => 'Servicio', 'en' => 'Service'],
    'misc.medicamento' => ['es' => 'Medicamento', 'en' => 'Medication'],
    'misc.alimento' => ['es' => 'Alimento', 'en' => 'Food'],
    'misc.accesorio' => ['es' => 'Accesorio', 'en' => 'Accessory'],
    'misc.servicio' => ['es' => 'Servicio', 'en' => 'Service'],
    'misc.otro' => ['es' => 'Otro', 'en' => 'Other'],
    'misc.allProducts' => ['es' => 'Todos', 'en' => 'All Products'],
];

foreach ($additions as $path => $vals) {
    setNested($es, $path, $vals['es']);
    setNested($en, $path, $vals['en']);
}

file_put_contents('public/locales/es.json', json_encode($es, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents('public/locales/en.json', json_encode($en, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Updated locales successfully.\n";
