<?php

namespace app\modules\api\controllers\v1;

use app\models\ar\personal\Personal;
use app\models\ar\Users;
use app\modules\api\commons\v1\ReturnCodes;
use app\modules\scarAuth\api\ClientV1;
use app\modules\scarAuth\api\AuthConnectorFactory;
use yii\web\Controller;


class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    public function actionLogin()
    {
        $email = \Yii::$app->request->post('email');
        $password = \Yii::$app->request->post('password'); // Закодированный в md5

        if (!$email || !$password) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);
            return [
                'message' => 'Логин или пароль не указаны',
                'errors' => [
                    'email' => '',
                    'password' => ''
                ],
            ];
        }
        $user = Users::findOne(['email' => $email]);
        if (!$user) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'message' => 'Пользователь не найден в CRM. Требуется регистрация',
                'errors' => [],
            ];
        }

        $api = new ClientV1(AuthConnectorFactory::create());
        $response = $api->login($email, $password);
        if (!isset($response['guid'], $response['auth_token'])) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);
            return [
                'message' => $response,
                'errors' => [
                    'password' => ''
                ],
            ];
        }

        // Обновляем данные авторизации
        $user->auth_token = $response['auth_token'];
        $user->guid = $response['guid'];
        $user->save();

        $personals = Personal::find()->where(['user_id' => $user->id])->all();
        $personalFormatted = [];
        foreach ($personals as $personal) {
            $carwash = $personal->getCarwash()->one();
            $personalFormatted[] = [
                'id' => $personal->id,
                'carwash' => [
                    'id' => $carwash->id ?? null,
                    'name' => $carwash->name ?? '',
                    'avatar' => $carwash->avatar ?? '',
                    'address' => $carwash->address ?? '',
                    'timezone' => $carwash->timezone ?? 0,
                ],
                'post' => Personal::getPostLabel($personal->post) ?? 'Не указан',
                'firebaseToken' => $personal->firebase_token ?? ''
            ];
        }

        return [
            'auth_token' => $user->auth_token,
            'guid' => $user->guid,
            'profile' => [
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
                'patronymic' => $user->patronymic ?? '',
                'avatar' => $user->avatar ?? '',
                'phone' => $user->phone ?? '',
                'phone_verified' => (bool)($user->phone_verified ?? false),
                'email' => $user->email ?? '',
                'lang_id' => $user->lang_id ?? Users::LANG_DEFAULT,
            ],
            'personals' => $personalFormatted
        ];
    }

    public function actionCheckToken()
    {
        $guid = \Yii::$app->request->post('guid');
        $token = \Yii::$app->request->post('token');

        if (!$token || !$guid) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);
            return [
                'message' => 'Не все данные переданы',
            ];
        }
        $user = Users::findOne(['guid' => $guid]);
        if (!$user) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'message' => 'Пользователь не найден в CRM',
                'errors' => [],
            ];
        }
        if ($user->auth_token !== $token) {
            return [
                'valid' => false,
                'message' => 'Требуется повторная авторизация',
            ];
        }
        $personals = Personal::find()->where(['user_id' => $user->id])->all();
        $personalFormatted = [];
        foreach ($personals as $personal) {
            $carwash = $personal->getCarwash()->one();
            $personalFormatted[] = [
                'id' => $personal->id,
                'carwash' => [
                    'id' => $carwash->id ?? null,
                    'name' => $carwash->name ?? '',
                    'avatar' => $carwash->avatar ?? '',
                    'address' => $carwash->address ?? '',
                    'timezone' => $carwash->timezone ?? 0,
                ],
                'post' => Personal::getPostLabel($personal->post) ?? 'Не указан',
                'firebaseToken' => $personal->firebase_token ?? ''
            ];
        }

        return [
            'valid' => true,
            'message' => 'Токен валиден',
            'profile' => [
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
                'patronymic' => $user->patronymic ?? '',
                'avatar' => $user->avatar ?? '',
                'phone' => $user->phone ?? '',
                'phone_verified' => (bool)($user->phone_verified ?? false),
                'email' => $user->email ?? '',
                'lang_id' => $user->lang_id ?? Users::LANG_DEFAULT,
            ],
            'personals' => $personalFormatted
        ];
    }
}
