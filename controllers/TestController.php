<?php

namespace app\controllers;

use app\commons\helpers\DefaultCarwashParams;
use app\commons\helpers\SaleHelper;
use app\commons\notification\NotifyHelper;
use app\models\ar\personal\PersonalLog;
use app\models\ar\personal\PersonalNotification;
use app\models\Carwash;
use app\models\forms\profile\ProfileContactsForm;
use app\models\forms\profile\ProfileNotificationsForm;
use app\models\Personal;
use app\models\Users;
use app\modules\scarAuth\api\ClientV1;
use app\modules\scarAuth\api\AuthConnectorFactory;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class TestController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'when' => static function () {
                            return YII_ENV_DEV;
                        },
                    ],
                    [
                        'allow' => true,
                        'ips' => ['127.0.0.1', '::1'],
                    ],
                ],
            ],
        ];
    }

    public function actionGetSale()
    {
        $helper = new SaleHelper(4);

        $order = '';
    }

    public function actionUsers()
    {
        $auth = Yii::$app->authManager;

//----------------- Создание роли

//Создадим роль 'author'
//        $admin = $auth->getRole('admin');

//Не обязательно, но можно добавить описание роли
//        $admin->description = 'Админ';

//Сохраняем роль в базе данных
//        $auth->add($admin);

//-------------------Создание разрешения для создания поста

//        $permCreatePost = $auth->createPermission('perm_show');//создали объект
//        $permCreatePost->description = 'Разрешение просмотра';//добавили описание
//        $auth->add($permCreatePost);//создали запись в базе данных

//-------------------Привяжем разрешение к роли
//Роль - это родительский элемент. Разрешение дочерний элемент роли
//        $auth->addChild($admin, $permCreatePost);


//-------------------Роль и разрешение созданы. Посмотрим как этим пользоваться.

//Для начала назначим какому-то обстрактному пользователю с id=10 роль 'автор'
//        $auth->assign($admin, 2);

//Проверим разрешение и если оно есть, позволим создать пост в блоге
//Так как разрешение 'perm_create-post' ранее было присоеденино к роли 'author',то
//пользователь с id=10 сможет создать пост
        var_dump(\Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->pid));

    }

    public function actionUser()
    {
        $form = new ProfileNotificationsForm(5);

        $form->systemTelegram = true;
        $form->systemWhatsapp = true;
        $form->eventNewOrder = true;
        $form->eventNewReview = false;

        var_dump($form);
    }

    public function actionPermissions()
    {
        echo 'PersonID: ' . \Yii::$app->user->pid . '<br>';
        echo 'CarwashID: ' . \Yii::$app->user->cwid . '<br>';
        echo 'Роли сотрудника: ';
        foreach (Yii::$app->authManager->getRolesByUser(\Yii::$app->user->pid) as $role) {
            echo $role->name . ';';
        }
        echo '<br>';
        echo 'Разрешения сотрудника: ';
        foreach (Yii::$app->authManager->getPermissionsByUser(\Yii::$app->user->pid) as $permission) {
            echo $permission->name . ';';
        }
    }

    public function actionTest()
    {

    }


    public function actionAuthService($email, $password)
    {
        $connector = AuthConnectorFactory::create();
        $client = new ClientV1($connector);
        var_dump($client->login($email, $password));
    }

    public function actionDefaults()
    {
        $helper = new DefaultCarwashParams(36);
//        $helper->createDefaultPrices();
//        $helper->defaultPersonal();
//        $helper->defaultClients();
        $helper->defaultOrders();
    }

    public function actionNotify()
    {
        $notify = new NotifyHelper();
        $notify->trigger(PersonalNotification::EVENT_NEW_PERSONAL);
    }

    public function actionRegenerateAuth()
    {
        Yii::$app->user->identity->getAuthKey();
    }

}
