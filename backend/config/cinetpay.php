<?php

return [
    'api_key' => env('CINETPAY_API_KEY', ''),
    'site_id' => env('CINETPAY_SITE_ID', ''),
    'secret_key' => env('CINETPAY_SECRET_KEY', ''),
    'notify_url' => env('CINETPAY_NOTIFY_URL', env('APP_URL') . '/api/payment/cinetpay/notify'),
];
