<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Livewire\Livewire;
use App\Models\User;

$user = User::first();
if ($user) {
    auth()->login($user);
}

$components = [
    \App\Livewire\Dashboard::class,
    \App\Livewire\Mascotas\MascotaIndex::class,
    \App\Livewire\Clientes\ClienteIndex::class,
    \App\Livewire\Citas\CitaIndex::class,
    \App\Livewire\Inventario\ProductoIndex::class,
    \App\Livewire\HistoriasClinicas\HistoriaClinicaIndex::class,
    \App\Livewire\Caja\CajaIndex::class,
    \App\Livewire\Caja\PuntoVenta::class,
    \App\Livewire\Reportes\ReporteIndex::class,
];

foreach ($components as $class) {
    try {
        $test = Livewire::test($class);
        echo "OK: $class\n";
    } catch (\Throwable $e) {
        echo "ERROR in $class: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}
