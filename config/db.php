<?php

$config = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . (getenv('DB_HOST') ?: 'mysql') . ';dbname=carwash',
    'username' => getenv('DB_USER') ?: 'carwash',
    'password' => getenv('DB_PASSWORD') ?: 'carwash',
    'charset' => 'utf8mb4',
];

$local = __DIR__ . '/db-local.php';
if (is_file($local)) {
    $config = array_merge($config, require $local);
}

return $config;
