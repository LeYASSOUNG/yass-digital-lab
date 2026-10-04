<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::raw('Ceci est un test de configuration SMTP pour Yass Digital Lab.', function($msg) { 
        $msg->to('diarrass1507@gmail.com')->subject('Test SMTP Laravel'); 
    });
    echo "Email envoyé avec succès!\n";
} catch (\Exception $e) {
    echo "Erreur d'envoi d'email: " . $e->getMessage() . "\n";
}
