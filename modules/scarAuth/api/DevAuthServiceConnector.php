<?php

namespace app\modules\scarAuth\api;

use Yii;

/**
 * Remote auth service connector. Configure host in params-local.php:
 * 'auth' => ['driver' => 'remote', 'host' => 'http://...', 'apiUrl' => '/api/v1/']
 */
class DevAuthServiceConnector extends ServiceConnector
{
    protected $host;
    protected $apiUrl = '/api/v1/';

    public function __construct($host = false, $apiUrl = false)
    {
        $this->host = $host ?: (Yii::$app->params['auth']['host'] ?? '');
        parent::__construct($host, $apiUrl);
    }
}
