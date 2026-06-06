<?php

namespace app\controllers;

use app\models\Carwash;
use app\models\Personal;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public $layout = 'app/main';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['policy'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'app/journal';

        return $this->render('table');
    }

    public function actionTable()
    {
        $this->layout = 'app/dev-journal';

        return $this->render('dev-table');
    }

    public function actionPolicy()
    {
        $this->layout = 'auth/main';
        return $this->render('policy');
    }


    public function actionDevTable()
    {
        $this->layout = 'app/journal';

        return $this->render('table');
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
//        $m = new Users();
//        $m->guid = '123123';
//        $m->auth_token = 'asdasdasdasdsad';
//        $m->status = 1;
//        $m->firstname = 'Aleks';
//        $m->lastname = 'Romanov';
//        $m->email = 'asdasdads@mail.ru';
//        $m->lang_id = '1';
//        $m->save();
//        $cw = new Carwash();
//        $cw->owner_id = '1';
//        $cw->city_id = null;
//        $cw->name = '22222';
//        $cw->avatar = '1231231231';
//        $cw->address = '123123';
//        $cw->save();
//       var_dump($cw->errors);

//            $p = new Personal();
//            $p->user_id = 1;
//            $p->carwash_id = 1;
//            $p->is_approved = 1;
//            $p->post = 1;
//            $p->salary_type = 1;
//            $p->salary = 1000;
//            $p->save();
//            var_dump($p->errors);


        echo '<pre>';
        var_dump(\Yii::$app->user->identity);
        echo '</pre>';
    }


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->response->cookies->remove('__pdata');
        return $this->goHome();
    }
}
