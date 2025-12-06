<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$productos = DB::table('productos')->get(['id', 'nombre', 'imagen']);

foreach ($productos as $producto) {
    echo $producto->id . ' | ' . $producto->nombre . ' | ' . ($producto->imagen ?? 'NULL') . PHP_EOL;
}