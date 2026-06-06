<?php

namespace app\modules\scarAuth\api;

use Yii;

class AuthConnectorFactory
{
    public static function create(): ServiceConnector
    {
        $driver = Yii::$app->params['auth']['driver'] ?? 'local';

        if ($driver === 'remote') {
            return new DevAuthServiceConnector();
        }

        return new LocalAuthServiceConnector();
    }
}
