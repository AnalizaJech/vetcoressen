<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$citas = App\Models\Appointment::latest('id')->take(10)->get();
foreach ($citas as $c) {
    echo "ID: {$c->id} | fecha_hora: {$c->fecha_hora} | end_time: {$c->end_time} | status: {$c->status}\n";
}
