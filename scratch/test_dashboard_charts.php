<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$d = new \App\Livewire\Dashboard();
$d->mount();

$ref = new ReflectionClass($d);
$mAtenciones = $ref->getMethod('obtenerAtencionesGrafico');
$mAtenciones->setAccessible(true);

$mIngresos = $ref->getMethod('obtenerIngresosGrafico');
$mIngresos->setAccessible(true);

echo "=== ATENCIONES HOY ===\n";
$d->filtroAtenciones = 'hoy';
print_r($mAtenciones->invoke($d));

echo "=== ATENCIONES SEMANA ===\n";
$d->filtroAtenciones = 'semana';
print_r($mAtenciones->invoke($d));

echo "=== ATENCIONES MES ===\n";
$d->filtroAtenciones = 'mes';
print_r($mAtenciones->invoke($d));

echo "=== ATENCIONES ANIO ===\n";
$d->filtroAtenciones = 'anio';
print_r($mAtenciones->invoke($d));

echo "=== INGRESOS HOY ===\n";
$d->filtroTiempo = 'hoy';
print_r($mIngresos->invoke($d, \Carbon\Carbon::today(), \Carbon\Carbon::today())->toArray());

echo "=== INGRESOS SEMANA ===\n";
$d->filtroTiempo = 'semana';
print_r($mIngresos->invoke($d, \Carbon\Carbon::today()->startOfWeek(), \Carbon\Carbon::today())->toArray());

echo "=== INGRESOS MES ===\n";
$d->filtroTiempo = 'mes';
print_r($mIngresos->invoke($d, \Carbon\Carbon::today()->startOfMonth(), \Carbon\Carbon::today())->toArray());

echo "=== INGRESOS ANIO ===\n";
$d->filtroTiempo = 'anio';
print_r($mIngresos->invoke($d, \Carbon\Carbon::today()->startOfYear(), \Carbon\Carbon::today())->toArray());
