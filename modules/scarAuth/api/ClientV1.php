<?php


namespace app\modules\scarAuth\api;

/**
 * Class ClientV1
 * @package app\modules\scarAuth\api
 */
class ClientV1
{
    protected $connector;

    public function __construct(ServiceConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param $email
     * @param $password
     * @return false|mixed|string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function register($email, $password)
    {
        $response = $this->connector->post('/register', ['email' => $email, 'password' => $password]);
        if (isset($response['error'])) {
            $code = $response['error']['code'] ?? null;
            $error = $response['error']['message'] ?? '';

            return $error;
        }
        return $response;
    }

    public function login($email, $password)
    {
        $response = $this->connector->post('/login', ['email' => $email, 'password' => $password]);
        if (isset($response['error'])) {
            $code = $response['error']['code'] ?? null;
            $error = $response['error']['message'] ?? '';

            return $error;
        }
        return $response;
    }

    public function resetPassword($email)
    {
        $response = $this->connector->post('/reset-password', ['email' => $email]);
        if (isset($response['error'])) {
            $code = $response['error']['code'] ?? null;
            $error = $response['error']['message'] ?? '';

            return $error;
        }
        return $response;
    }

    public function changePassword($email, $accessToken, $newPassword)
    {
        $response = $this->connector->post('/change-password', [
            'email' => $email,
            'accessToken' => $accessToken,
            'password' => $newPassword
        ]);

        if (isset($response['error'])) {
            $code = $response['error']['code'] ?? null;
            $error = $response['error']['message'] ?? '';

            return $error;
        }
        return $response;
    }

    public function changeEmail($guid, $email, $accessToken)
    {

    }

    public function changeStatus($guid, $status)
    {

    }

    public function checkToken($guid, $accessToken)
    {

    }

    public function getUserInfo($guid)
    {

    }


}