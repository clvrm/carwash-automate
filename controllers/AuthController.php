<?php


namespace app\controllers;


use app\commons\logs\db\EventLogger;
use app\commons\models\MailSender;
use app\models\ar\carwash\Carwash;
use app\models\ar\logs\EventLog;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalLog;
use app\models\ar\Users;
use app\models\LoginForm;
use app\modules\scarAuth\api\AuthConnectorFactory;
use app\modules\scarAuth\api\ClientV1;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;

/**
 * Class AuthController
 * @package app\controllers
 */
class AuthController extends Controller
{
    public $layout = '/auth/main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');

    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/');
        }
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();

            $email = $post['email'];
            $password = $post['password'];
            $rememberMe = $post['rememberMe'] ?? false;

            $user = Users::findOne(['email' => $email]);
            if ($user) {
                $error = 'Данный email уже зарегистрирован';
            } else {
                $api = new ClientV1(AuthConnectorFactory::create());
                $response = $api->register($email, $password);

                if (isset($response['guid'], $response['auth_token'])) {
                    $userModel = new Users();
                    $userModel->email = $email;
                    $userModel->auth_token = $response['auth_token'];
                    $userModel->guid = $response['guid'];
                    $userModel->setPassword($password);

                    if ($userModel->save()) {
                        $userModel->refresh();
                        // Регистрируем автомойку для владельца
                        $carwash = new Carwash();
                        $carwash->name = Carwash::DEFAULT_NAME;
                        $carwash->save();
                        $carwash->refresh();
                        // Первичные настройки для автомойки
                        $carwash->createDefaultSettings();

                        // Регистрируем владельца, как персонал своей же автомойки
                        $personal = new Personal();
                        $personal->carwash_id = $carwash->id;
                        $personal->user_id = $userModel->id;
                        $personal->post = Personal::POST_OWNER;
                        $personal->save();
                        // Даем персоналу права владельца на старте
                        Yii::$app->authManager->assign(Yii::$app->authManager->getRole('owner'), $personal->id);

                        // Связываем владельца, в лице персонала с его же автомойкой
                        $carwash->owner_id = $personal->id;
                        $carwash->save();

                        $this->auth($userModel, $rememberMe);
                        $mailSender = new MailSender();
                        $mailSender->sendRegisterEmail($email, $userModel->id, $personal->id);

                        return $this->redirect(['/auth/register-success', 'email' => $email]);
                    }
                } else {
                    $error = $response;
                }
            }
        }

        return $this->render('register', [
            'error' => $error ?? ''
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/');
        }
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();

            $email = $post['email'];
            $password = $post['password'];
            $rememberMe = $post['rememberMe'] ?? false;

            $user = Users::findOne(['email' => $email]);

            $api = new ClientV1(AuthConnectorFactory::create());
            $response = $api->login($email, $password);
            if (isset($response['guid'], $response['auth_token'])) {
                if ($user) {
                    $user->auth_token = $response['auth_token'];
                    $user->guid = $response['guid'];
                    $user->save();

                    $personal = Personal::findAll(['user_id' => $user->id]);

                    if (count($personal) === 1) {
                        $this->auth($user, $rememberMe);
                        // Если у пользователя не заполнены базовые данные - заставляем заполнить
                        if (empty($user->firstname)) {
                            return $this->redirect('/carwash/base-setting');
                        }

                        return $this->redirect('/site/index');
                    }
                    if (count($personal) > 1) {
                        $this->auth($user, $rememberMe);
                        // Пользователь участник двух и более моек
                        return $this->redirect('/auth/select-account');
                    }

                    $error = 'Вы не состоите ни в одной автомойке на данный момент';
                } else {
                    // Пользователь уже существует в сервисе авторизации
                    $user = new Users();
                    $user->email = $email;
                    $user->auth_token = $response['auth_token'];
                    $user->guid = $response['guid'];

                    if ($user->save()) {
                        $user->refresh();
                        // Регистрируем автомойку для владельца
                        $carwash = new Carwash();
                        $carwash->name = Carwash::DEFAULT_NAME;
                        $carwash->save();
                        $carwash->refresh();
                        // Первичные настройки для автомойки
                        $carwash->createDefaultSettings();

                        // Регистрируем владельца, как персонал своей же автомойки
                        $personal = new Personal();
                        $personal->carwash_id = $carwash->id;
                        $personal->user_id = $user->id;
                        $personal->is_approved = Personal::IS_APPROVED;
                        $personal->post = Personal::POST_OWNER;
                        $personal->save();
                        // Даем персоналу права владельца на старте
                        Yii::$app->authManager->assign(Yii::$app->authManager->getRole('owner'), $personal->id);

                        // Связываем владельца, в лице персонала с его же автомойкой
                        $carwash->owner_id = $personal->id;
                        $carwash->save();

                        $this->auth($user, $rememberMe);

                        return $this->redirect('/carwash/base-setting');
                    }
                }
            } else {
                $error = $response;
            }
        }

        return $this->render('login', [
            'error' => $error ?? ''
        ]);
    }

    /**
     * @param $code
     * @return \yii\web\Response
     */
    public
    function actionRegisterSubmit($code)
    {
        $log = EventLog::findOne(['type' => EventLog::TYPE_SYSTEM, 'event' => EventLog::SYS_USER_REGISTER_EMAIL, 'data' => $code]);
        // TODO: Валидация по времени жизни кода
        if ($log) {
            if (!empty($log->personal_id)) {
                $personal = Personal::findOne(['id' => $log->personal_id]);
                if ($personal && $personal->is_approved === Personal::IS_NOT_APPROVED) {
                    Yii::$app->session->setFlash('success', 'Ваш email успешно подтвержден');

                    $personal->is_approved = Personal::IS_APPROVED;
                    $personal->save();
                } else {
                    Yii::$app->session->setFlash('success', 'Ваш email уже был подтверждён');
                }
            }
        } else {
            Yii::$app->session->setFlash('danger', 'Ссылка для авторизации устарела. Отправьте новый код на странице профиля');
        }
        // На всякий случай, очищаем
        \Yii::$app->user->logout();
        \Yii::$app->response->cookies->remove('__pdata');

        if (Yii::$app->user->isGuest) {
            return $this->redirect('/auth/login');
        }
        return $this->redirect('/site/index');
    }

    /**
     * @return \yii\web\Response
     */
    public
    function actionLogout()
    {
        try {
            $log = new PersonalLog();
            $log->createLog(\Yii::$app->user->identity->getPId() ?? 0, '', PersonalLog::AUTH_LOGOUT);
        } catch (\Exception $exception) {
        }

        \Yii::$app->user->logout();
        \Yii::$app->response->cookies->remove('__pdata');

        return $this->redirect('/auth/login');
    }

    /**
     * @param $email
     * @return string
     */
    public
    function actionRegisterSuccess($email)
    {
        return $this->render('register-success', [
            'email' => $email
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public
    function actionReset()
    {
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $email = $post['email'];

            $user = Users::findOne(['email' => $email]);

            $api = new ClientV1(AuthConnectorFactory::create());
            $response = $api->resetPassword($email);

            if ($user) {
                if (isset($response['reset_token'], $response['guid'])) {
                    $token = md5($response['reset_token'] . $user->id);

                    $mailSender = new MailSender();
                    $mailSender->sendResetEmail($email, $user->id, $token);

                    return $this->redirect(['/auth/reset-success', 'email' => $email]);
                }
            } else {
                $error = 'Пользователь не найден';
            }
        }

        return $this->render('reset-password', [
            'error' => $error ?? ''
        ]);
    }

    /**
     * @param false $resetCode
     * @return string|\yii\web\Response
     */
    public
    function actionChangePassword($resetCode = false)
    {
        if (Yii::$app->user->isGuest) {
            if (!$resetCode) {
                Yii::$app->session->setFlash('danger', 'У вас нет прав для просмотра данной страницы');

                return $this->redirect('/auth/login');
            }
            $log = EventLog::find()->where(['event' => EventLog::SYS_USER_RESET_EMAIL, 'data' => $resetCode])
                ->andWhere('created_at BETWEEN DATE_ADD(NOW(), INTERVAL -1 DAY) AND NOW()')
                ->orderBy('created_at DESC')->one();
            if (!$log) {
                Yii::$app->session->setFlash('danger', 'Ссылка для восстановления пароля устарела');

                return $this->redirect('/auth/login');
            }

            $userId = $log->user_id;
        } else {
            $userId = Yii::$app->user->getId();
        }
        if (Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $newPassword = $post['password'];
            $user = Users::findOne($userId);
            if (!$user) {
                Yii::error('Не удалось найти пользователя при смене пароля');
            }
            // Если пользователь только приглашен - у него нет токена и его нет в базе авторизации
            if (!$user->auth_token) {
                if (empty($user->guid)) {
                    $user->guid = Yii::$app->security->generateRandomString(32);
                }
                if (empty($user->password_hash)) {
                    $user->setPassword(Users::INVITED_DEFAULT_PASSWORD);
                }
                $user->generateAuthKey();
                $user->save(false);
            }

            $api = new ClientV1(AuthConnectorFactory::create());
            $response = $api->changePassword($user->email, $user->auth_token, $newPassword);
            if (isset($response['guid'], $response['auth_token'])) {
                $user->auth_token = $response['auth_token'];
                $user->save();
                Yii::$app->session->setFlash('success', 'Ваш пароль обновлен');
                return $this->redirect('/auth/login');
            } else {
                $error = $response;
            }
        }

        return $this->render('/auth/change-password', [
            'error' => $error ?? ''
        ]);
    }

    /**
     * @param $pid
     * @return \yii\web\Response
     */
    public
    function actionAcceptInvite($pid)
    {
        $log = EventLog::find()->where(['event' => EventLog::SYS_USER_INVITE_EMAIL, 'personal_id' => $pid])->one();
        $personal = Personal::findOne(['id' => $pid]);
        if (empty($log) || !$personal) {
            Yii::$app->session->setFlash('danger', 'Запросите новую ссылку-приглашение');

            return $this->redirect('/auth/login');
        }
        $personal->is_approved = 1;
        $personal->save();
        $user = Users::findOne(['id' => $personal->user_id]);

        $this->auth($user);
        Yii::$app->session->setFlash('success', 'Вы успешно приняли приглашение');

        // TODO: Подумать что делать с возвращаемой страницей
        return $this->redirect('/auth/login');
    }

    /**
     * @param $email
     * @return string
     */
    public
    function actionResetSuccess($email)
    {
        return $this->render('reset-success', [
            'email' => $email
        ]);
    }

    /**
     * Выбор аккаунта для авторизации
     * @return string|\yii\web\Response
     * @throws \Throwable
     */
    public
    function actionSelectAccount()
    {
        $user = Yii::$app->user->getIdentity();
        if (!$user) {
            Yii::$app->session->setFlash('danger', 'Вы не авторизованы');
            return $this->redirect('/auth/login');
        }
        $personals = Personal::findAll(['user_id' => $user->getId()]);

        if (Yii::$app->request->isPost) {
            $personalId = Yii::$app->request->post('personalId');
            $this->auth($user, true, $personalId);
            $this->redirect('/site/index');
        }

        return $this->render('select-account', [
            'personal' => $personals
        ]);
    }

    /**
     * Смена аккаунта
     * @param $cPId
     * @param $sPId
     */
    public
    function actionSwitchAccount($cPId, $sPId)
    {
        $currentPersonal = Personal::findOne($cPId);
        $selectedPersonal = Personal::findOne($sPId);
        if (!$currentPersonal || !$selectedPersonal) {
            Yii::$app->session->setFlash('danger', 'Выбранный аккаунт недоступен');
            \Yii::$app->user->logout();
            \Yii::$app->response->cookies->remove('__pdata');
            return $this->redirect('/auth/login');
        }
        if ($currentPersonal->user_id !== $selectedPersonal->user_id || $currentPersonal->user_id !== Yii::$app->user->getId()) {
            Yii::$app->session->setFlash('danger', 'Данные пользователя не совпадают');
            \Yii::$app->user->logout();
            \Yii::$app->response->cookies->remove('__pdata');
            return $this->redirect('/auth/login');
        }
        $user = Users::findOne(Yii::$app->user->getId());
        $this->auth($user, true, $selectedPersonal);

        return $this->redirect('/site/index');
    }

    /**
     * Принудительная авторизация конкретного пользователя
     * @param Users $user
     * @param bool $rememberMe
     * @return bool
     */
    private
    function auth(Users $user, bool $rememberMe = false, $personalId = false)
    {
        if ($personalId) {
            $personal = Personal::findOne($personalId);
            if ($personal && $personal->user_id !== $user->id) {
                Yii::$app->session->setFlash('danger', 'Ошибка при авторизации в выбранную автомойку');
                return false;
            }
        } else {
            $personal = Personal::findOne(['user_id' => $user->id]);
        }
        try {
            $log = new PersonalLog();
            $log->createLog($personal->id ?? 0, '', PersonalLog::AUTH_LOGIN);
        } catch (\Exception $exception) {
        }
        // Авторизационная кука для входа
        Users::setUserLoginCookie($personal->id);
        return \Yii::$app->user->login($user, $rememberMe ? 3600 * 24 * 365 : 0);
    }
}