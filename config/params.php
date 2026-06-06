<?php

$params = [
    'auth' => [
        'driver' => 'local',
    ],
    'app.baseUrl' => getenv('APP_BASE_URL') ?: 'http://localhost:8000',
    'restrictions' => [
        'carwash-max-images' => 5,
    ],
    'contacts' => [
        'admin-email' => 'admin@localhost',
    ],
    'user.passwordResetTokenExpire' => 3600,
];

$local = __DIR__ . '/params-local.php';
if (is_file($local)) {
    $params = array_replace_recursive($params, require $local);
}

return $params;
