<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::latest()->first();
if ($user) {
    $user->notify(new App\Notifications\SystemNotification('promo', 'Bienvenue sur Yass Digital Lab !', 'Votre compte est désormais activé. Explorez notre catalogue de templates SaaS et packs IA.', '/products'));
    echo "Notification envoyee a " . $user->email . "\n";
} else {
    echo "Aucun utilisateur trouve\n";
}
