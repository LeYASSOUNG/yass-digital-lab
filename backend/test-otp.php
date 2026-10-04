<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = \App\Models\User::where('email', 'diarrass1507@gmail.com')->first();
    if ($user) {
        $user->generateOtp();
        $user->notify(new \App\Notifications\ResetPasswordOtpNotification());
        echo "SUCCESS_OTP_SENT\n";
    } else {
        echo "USER_NOT_FOUND\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
