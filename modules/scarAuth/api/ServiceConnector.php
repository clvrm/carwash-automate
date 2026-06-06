<?php


namespace app\modules\scarAuth\api;


use yii\httpclient\Client;

/**
 * Class ServiceConnector
 * @package app\modules\scarAuth\api
 */
abstract class ServiceConnector
{
    protected $host;
    protected $apiUrl;
    private $client;

    /**
     * ServiceConnector constructor.
     * @param false $host
     * @param false $apiUrl
     */
    public function __construct($host = false, $apiUrl = false)
    {
        if (!$host) {
            $host = $this->host;
        }
        if (!$apiUrl) {
            $apiUrl = $this->apiUrl;
        }

        $baseUrl = $host . $apiUrl;
        $this->client = new Client(['baseUrl' => $baseUrl]);
    }

    /**
     * @param $url
     * @param $method
     * @param array $data
     * @return false|mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function request($url, $method, $data = [])
    {
        $response = $this->client->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setData($data)
            ->send();
        if ($response->isOk) {
            return $response->data;
        }
        if ($response->statusCode == 500) {
            \Yii::error('Ошибка сервера при запросе к сервису авторизации', 'auth-service');
        } else {
            \Yii::error('Произошла ошибка при запросе к сервису авторизации', 'auth-service');
        }
        return false;
    }


    /**
     * @param $url
     * @param array $data
     * @return false|mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function get($url, $data = [])
    {
        return $this->request($url, 'GET', $data);
    }


    /**
     * @param $url
     * @param array $data
     * @return false|mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function post($url, $data = [])
    {
        return $this->request($url, 'POST', $data);
    }
}