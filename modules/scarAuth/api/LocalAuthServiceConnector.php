<?php

namespace app\modules\scarAuth\api;

use app\models\ar\Users;
use Yii;

class LocalAuthServiceConnector extends ServiceConnector
{
    public function post($url, $data = [])
    {
        switch ($url) {
            case '/register':
                return $this->register($data['email'] ?? '', $data['password'] ?? '');
            case '/login':
                return $this->login($data['email'] ?? '', $data['password'] ?? '');
            case '/reset-password':
                return $this->resetPassword($data['email'] ?? '');
            case '/change-password':
                return $this->changePassword(
                    $data['email'] ?? '',
                    $data['accessToken'] ?? '',
                    $data['password'] ?? ''
                );
            default:
                return false;
        }
    }

    private function register(string $email, string $password)
    {
        if (Users::findOne(['email' => $email])) {
            return 'Данный email уже зарегистрирован';
        }

        return [
            'guid' => Yii::$app->security->generateRandomString(32),
            'auth_token' => Yii::$app->security->generateRandomString(48),
        ];
    }

    private function login(string $email, string $password)
    {
        $user = Users::findOne(['email' => $email]);
        if (!$user) {
            return 'Пользователь не найден';
        }
        if (!$user->validatePassword($password)) {
            return 'Неверный email или пароль';
        }

        return [
            'guid' => $user->guid,
            'auth_token' => Yii::$app->security->generateRandomString(48),
        ];
    }

    private function resetPassword(string $email)
    {
        $user = Users::findOne(['email' => $email]);
        if (!$user) {
            return 'Пользователь не найден';
        }

        return [
            'reset_token' => Yii::$app->security->generateRandomString(32),
            'guid' => $user->guid,
        ];
    }

    private function changePassword(string $email, string $accessToken, string $newPassword)
    {
        $user = Users::findOne(['email' => $email, 'auth_token' => $accessToken]);
        if (!$user) {
            return 'Ошибка авторизации';
        }

        $user->setPassword($newPassword);
        $user->auth_token = Yii::$app->security->generateRandomString(48);
        $user->save(false);

        return [
            'guid' => $user->guid,
            'auth_token' => $user->auth_token,
        ];
    }
}
