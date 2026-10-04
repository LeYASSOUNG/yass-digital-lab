<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::raw('Ceci est un test de configuration SMTP pour Yass Digital Lab.', function($message) {
        $message->to('diarrass1507@gmail.com')->subject('Test SMTP Configuration');
    });
    echo "SUCCESS_MAIL_SENT\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
