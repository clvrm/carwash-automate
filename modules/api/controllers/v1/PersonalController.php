<?php

namespace app\modules\api\controllers\v1;

use app\commons\helpers\FileHelper;
use app\models\ar\carwash\Carwash;
use app\models\ar\personal\Personal;
use app\models\ar\rbac\AuthAssignment;
use app\models\ar\Users;
use app\models\forms\profile\ProfileNotificationsForm;
use app\modules\api\commons\ApiHelper;
use app\modules\api\commons\v1\ReturnCodes;
use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;

class PersonalController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }


    public function actionPermissions()
    {
        $pId = Yii::$app->request->post('pId');
        $token = Yii::$app->request->post('token');
        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);
            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }

        $permissions = AuthAssignment::findAll(['personal_id' => $pId]);
        $permissionsArray = [];
        foreach ($permissions as $permission) {
            $permissionsArray[] = $permission->item_name;
        }

        return ['permissions' => $permissionsArray];
    }

    public function actionList()
    {
        $pId = Yii::$app->request->post('pId');
        $cwId = Yii::$app->request->post('cwId');
        $token = Yii::$app->request->post('token');
        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);
            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'personal' => [],
                'message' => 'Не найдены автомойка или сотрудник'
            ];
        }
        if ($carwash->id !== $personal->carwash_id) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ACCESS_FORBIDDEN);
            return [
                'message' => 'Доступ запрещен',
                'errors' => [
                    'pId' => 'Нет доступа к списку сотрудников'
                ],
            ];
        }
        $personals = Personal::findAll(['carwash_id' => $cwId]);
        $personalArray = [];

        foreach ($personals as $personal) {
            $user = $personal->getUser()->one();
            $personalArray[] = [
                'id' => $personal->id,
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
                'patronymic' => $user->patronymic ?? '',
                'avatar' => $user->avatar ?? '',
                'phone' => $user->phone ?? '',
                'email' => $user->email ?? '',
                'post' => $personal::getPostLabel($personal->post) ?? '',
            ];
        }

        return ['personal' => $personalArray];
    }

    public function actionProfile($token, $pId)
    {
        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);
            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        $personal = Personal::findOne($pId);
        if (!$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'pId' => 'Не найдены автомойка или сотрудник'
                ],
                'message' => 'Не найдены автомойка или сотрудник'
            ];
        }
        $user = $personal->getUser()->one();

        return [
            'profile' => [
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
                'patronymic' => $user->patronymic ?? '',
                'avatar' => $user->avatar ?? '',
                'phone' => $user->phone ?? '',
                'phone_verified' => (bool)($user->phone_verified ?? false),
                'email' => $user->email ?? '',
                'lang_id' => $user->lang_id ?? Users::LANG_DEFAULT,
            ]
        ];
    }

    public function actionEditProfile()
    {
        $pId = Yii::$app->request->post('pId');
        $token = Yii::$app->request->post('token');
        $firebaseToken = Yii::$app->request->post('firebaseToken');
        $firstname = Yii::$app->request->post('firstname');
        $lastname = Yii::$app->request->post('lastname');
        $patronymic = Yii::$app->request->post('patronymic');
        $phone = Yii::$app->request->post('phone');
        $email = Yii::$app->request->post('email');

        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);
            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        $personal = Personal::findOne($pId);
        if (!$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'pId' => 'Не найден сотрудник'
                ],
                'message' => 'Не найден сотрудник'
            ];
        }

        $user = $personal->getUser()->one();
        if (!$user) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'pId' => 'Не найден профиль сотрудника'
                ],
                'message' => 'Не найден профиль сотрудника'
            ];
        }
        if (isset($firstname)) {
            $user->firstname = $firstname;
        }
        if (isset($firebaseToken)) {
            $personal->firebase_token = $firebaseToken;
        }
        if (isset($lastname)) {
            $user->lastname = $lastname;
        }
        if (isset($patronymic)) {
            $user->patronymic = $patronymic;
        }
        if (isset($email)) {
            $user->email = $email;
        }
        if (isset($phone)) {
            $user->phone = $phone;
        }
        $user->save();
        $personal->save();

        return [
            'profile' => [
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
                'patronymic' => $user->patronymic ?? '',
                'avatar' => $user->avatar ?? '',
                'phone' => $user->phone ?? '',
                'phone_verified' => (bool)($user->phone_verified ?? false),
                'email' => $user->email ?? '',
                'firebaseToken' => $personal->firebase_token ?? '',
                'lang_id' => $user->lang_id ?? Users::LANG_DEFAULT,
            ]
        ];
    }

    public function actionUploadImage()
    {
        $pId = \Yii::$app->request->post('pId');
        $token = \Yii::$app->request->post('token');
        $file = \Yii::$app->request->post('file');

        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);
            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        $personal = Personal::findOne($pId);
        if (!$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'pId' => 'Не найден сотрудник'
                ],
                'message' => 'Не найден сотрудник'
            ];
        }

        $user = $personal->getUser()->one();
        if (!$user) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'pId' => 'Не найден профиль сотрудника'
                ],
                'message' => 'Не найден профиль сотрудника'
            ];
        }

        $uploadedFile = UploadedFile::getInstanceByName('file');
        if (!empty($uploadedFile)) {
            $avatarLink = FileHelper::saveUploaded($uploadedFile, \Yii::getAlias('@user-avatars') . $user->id . '/', true);
            if ($avatarLink) {
                $user->avatar = $avatarLink;
                $user->save();

                return ['result' => true, 'avatar' => $avatarLink];
            }
        }

        return ['result' => false, 'error' => true, 'message' => 'Выберите другой файл'];
    }

    public function actionChangePhone()
    {
        // TODO: Реализовать
    }

    public function actionLang()
    {
        $pId = Yii::$app->request->post('pId');
        $token = Yii::$app->request->post('token');
        $lang = Yii::$app->request->post('lang');

        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);
            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        $personal = Personal::findOne($pId);
        $user = Users::findOne($personal->user_id ?? 0);
        if (!$personal || !$user) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'pId' => 'Не найден сотрудник'
                ],
                'message' => 'Не найден сотрудник'
            ];
        }

        $user->lang_id = $lang;
        return ['result' => $user->save()];
    }

    /**
     * @throws \app\commons\exceptions\LogicException
     */
    public function actionNotify()
    {
        $pId = Yii::$app->request->post('pId');
        $cwId = Yii::$app->request->post('cwId');
        $token = Yii::$app->request->post('token');
        $notification = Yii::$app->request->post('notification');
        $firebaseUid = Yii::$app->request->post('firebaseUid');

        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(420);
            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            \Yii::$app->response->setStatusCode(404);
            return [
                'errors' => [
                    'pId' => 'Не найдены автомойка или сотрудник'
                ],
                'message' => 'Не найдены автомойка или сотрудник'
            ];
        }
        if ($carwash->id !== $personal->carwash_id) {
            \Yii::$app->response->setStatusCode(404);
            return [
                'message' => 'Доступ запрещен',
                'errors' => [
                    'pId' => 'Нет доступа к списку сотрудников'
                ],
            ];
        }
        $form = new ProfileNotificationsForm($pId);
        $form->load(\Yii::$app->request->post(), 'notification');
        if (isset($firebaseUid)) {
            $form->firebaseUid = $firebaseUid;
        }
        $form->save();
        return ['result' => true];

    }
}
