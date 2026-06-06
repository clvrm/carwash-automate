<?php


namespace app\controllers;


use app\commons\exceptions\LogicException;
use app\commons\models\MailSender;
use app\commons\notification\NotifyHelper;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSales;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\rbac\AuthItem;
use app\models\ar\Users;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class PersonalController
 * @package app\controllers
 */
class PersonalController extends Controller
{
    public $layout = 'app/main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['perm_edit_personal'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $cwId = \Yii::$app->user->identity->getCWid();

        $personals = Personal::find()->where(['carwash_id' => $cwId])->orderBy('post ASC')->all();

        return $this->render('index', [
            'personals' => $personals
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionCreate()
    {
        $carwash = Carwash::findOne([\Yii::$app->user->identity->getCwId()]);

        $model = new Personal();

        if (\Yii::$app->request->isPost) {
            $model->carwash_id = $carwash->id;
            $user = Users::findOne(['email' => \Yii::$app->request->post('email')]);
            if (empty($user)) {
                $user = new Users();
                $user->email = \Yii::$app->request->post('email');
                $user->status = Users::STATUS_FIRST_INVITED;
                $user->guid = Users::INVITED_GUID . time();
                $user->save();
            } else {
                $findedPersonal = Personal::findOne(['user_id' => $user->id, 'carwash_id' => $carwash->id]);
                if ($findedPersonal) {
                    \Yii::$app->session->setFlash('danger', 'Пользователь уже существует');
                    return $this->redirect('/personal/create');
                }
            }
            $model->user_id = $user->id;

            $model->load(\Yii::$app->request->post());
            $model->validate();
            if ($model->save()) {
                $permissions = \Yii::$app->request->post('Perm') ?? [];
                $post = $model->post;
                $this->assignPersonPermissions($model->id, $post, $permissions);
                $mailer = new MailSender();
                $mailer->sendInviteEmail($user->email, $user->id, $carwash->id, $model->id);
                \Yii::$app->session->setFlash('success', 'Приглашение пользователю отправлено');
                $notify = new NotifyHelper();
                $notify->trigger(PersonalNotification::EVENT_NEW_PERSONAL);
                return $this->redirect('/personal/index');
            }
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionEdit($id)
    {
        $carwash = Carwash::findOne([\Yii::$app->user->identity->getCwId()]);

        $model = Personal::findOne(['carwash_id' => $carwash->id, 'id' => $id]);
        if (!$carwash | !$model){
            throw new LogicException('Не найдены базовые модели для работы с персоналом');
        }
        $isPersonalOwner = ($model->id === $carwash->owner_id);
        $personPermissions = \Yii::$app->authManager->getPermissionsByUser($model->id);
        if (!$model) {
            \Yii::$app->session->setFlash('danger', 'Пользователь не найден');
            return $this->redirect('/personal/index');
        }
        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            if ($model->isAttributeChanged('salary_type',false) || $model->isAttributeChanged('salary',false)){
                $notify = new NotifyHelper();
                $notify->trigger(PersonalNotification::EVENT_EDIT_PERSONAL_SALARY,
                    ['initiatorPersonalId' => \Yii::$app->user->identity->getPId()]);
            }
            $model->validate();
            if ($model->save()) {
                $permissions = \Yii::$app->request->post('Perm') ?? [];
                if ($isPersonalOwner){
                    $permissions['perm_edit_personal'] = true;
                }
                $post = $model->post;
                $this->assignPersonPermissions($model->id, $post, $permissions);
                if ($model->is_approved) {
                    \Yii::$app->session->setFlash('success', 'Настройки пользователя обновлены');
                } else {
                    \Yii::$app->session->setFlash('success', 'Приглашение пользователю отправлено');
                }
                return $this->redirect('/personal/index');
            }
        }


        return $this->render('edit', [
            'model' => $model,
            'isPersonalOwner' => $isPersonalOwner,
            'permissions' => array_keys($personPermissions)
        ]);
    }

    /**
     * @param $personId
     * @param $role
     * @param $permissions
     * @throws \Exception
     */
    private function assignPersonPermissions($personId, $role, $permissions)
    {
        $auth = \Yii::$app->authManager;

        // Сбрасываем старые разрешения
        $auth->revokeAll($personId);

        switch ($role) {
            case Personal::POST_MANAGER:
                $roleName = AuthItem::ROLE_MANAGER;
                break;
            case Personal::POST_ADMIN:
                $roleName = AuthItem::ROLE_ADMIN;
                break;
            default:
                $roleName = AuthItem::ROLE_WASHER;
                break;
        }

        $role = $auth->getRole($roleName);
        $auth->assign($role, $personId);

        foreach ($permissions as $key => $permission) {
            $permissionItem = $auth->getPermission($key);
            $auth->assign($permissionItem, $personId);
        }
    }
}