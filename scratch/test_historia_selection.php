<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Livewire\Livewire;
use App\Models\User;
use App\Models\Customer;

$user = User::first();
if ($user) {
    auth()->login($user);
}

$customer = Customer::with('mascotas.historiasClinicas')->first();
echo "Testing client selection for: " . $customer->nombre_completo . " (ID: {$customer->id})\n";

try {
    $component = Livewire::test(\App\Livewire\HistoriasClinicas\HistoriaClinicaIndex::class)
        ->call('seleccionarCliente', $customer->id)
        ->assertSet('clienteSeleccionadoId', $customer->id);
    echo "SUCCESS: Client selection rendered without any exceptions!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}
