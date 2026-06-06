<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$aliases = require __DIR__ . '/aliases.php';

$config = [
    'id' => 'carwash',
    'name' => 'Carwash — личный кабинет автомойки',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => $aliases,
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\AdminModule',
        ],
        'api' => [
            'class' => 'app\modules\api\ApiModule',
        ],
        'carwash-auth' => [
            'class' => 'app\modules\scarAuth\StepcarAuthModule',
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'app\commons\yii\DbManager',
        ],
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'XKXgEBqF-Vff7CX1bInIUhQuUcZak2vH',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'app\commons\yii\rbac\User',
            'identityClass' => 'app\models\ar\Users',
            'loginUrl' => '/auth/login',
            'enableAutoLogin' => true,
            'autoRenewCookie' => true,
            'authTimeout' => 30,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*', '::1'],
    ];
}

return $config;
