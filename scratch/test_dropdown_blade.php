<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$view = \Illuminate\Support\Facades\Blade::render('
    <x-vc-dropdown wire:model.live="filtroAtenciones" :options="[[\'value\'=>\'hoy\',\'label\'=>\'Hoy\']]" />
');
echo $view;
