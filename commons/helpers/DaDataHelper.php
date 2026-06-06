<?php

namespace app\commons\helpers;

use Dadata\DadataClient;
use Yii;

class DaDataHelper
{
    private static $client;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getClient(): ?DadataClient
    {
        $token = Yii::$app->params['dadata']['token'] ?? '';
        $secret = Yii::$app->params['dadata']['secret'] ?? '';

        if ($token === '' || $secret === '') {
            return null;
        }

        if (self::$client === null) {
            self::$client = new DadataClient($token, $secret);
        }

        return self::$client;
    }

    public static function getByIp($ip = false)
    {
        $client = self::getClient();
        if ($client === null) {
            return false;
        }
        if (!$ip) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        }

        return $client->iplocate($ip);
    }

    public static function getCityByIp($ip = false)
    {
        try {
            $client = self::getClient();
            if ($client === null) {
                return false;
            }
            if (!$ip) {
                $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            }
            $response = $client->iplocate($ip);

            return $response['data']['city'] ?? false;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
