<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Mapeo de productos con sus imágenes
$imagenes = [
    'Crema Hidratante Facial' => 'images/products/cremafacial.jpg',
    'Serum Vitamina C' => 'images/products/serumvitamina.jpg',
    'Exfoliante Corporal' => 'images/products/exfoliantecorporal.jpg',
    'Aceite de Masaje Relajante' => 'images/products/aceitemasajes.jpg',
    'Mascarilla Purificante' => 'images/products/mascarillapurificante.jpg',
    'Esmalte Semipermanente' => 'images/products/semaltesemipermanent.jpg',
    'Loción Corporal' => 'images/products/locion.jpg',
];

foreach ($imagenes as $nombre => $rutaImagen) {
    $updated = DB::table('productos')
        ->where('nombre', $nombre)
        ->update(['imagen' => $rutaImagen]);

    if ($updated) {
        echo "✓ Actualizado: $nombre -> $rutaImagen\n";
    } else {
        echo "✗ No se encontró: $nombre\n";
    }
}

echo "\n¡Imágenes actualizadas correctamente!\n";