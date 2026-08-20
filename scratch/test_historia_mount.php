<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cust = \App\Models\Customer::has('mascotas')->first();
echo "Found customer ID: {$cust->id} ({$cust->nombre_completo})\n";

// Simulate mount with request
request()->merge(['clienteSeleccionadoId' => $cust->id]);
$comp = new \App\Livewire\HistoriasClinicas\HistoriaClinicaIndex();
$comp->mount();
echo "Component clienteSeleccionadoId after mount: {$comp->clienteSeleccionadoId}\n";
$view = $comp->render();
echo "Rendered view successfully for selected client!\n";
