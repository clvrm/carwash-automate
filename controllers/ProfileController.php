<?php


namespace app\controllers;


use app\commons\exceptions\LogicException;
use app\commons\helpers\FileHelper;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalLog;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\Users;
use app\models\forms\profile\ProfileContactsForm;
use app\models\forms\profile\ProfileNotificationsForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class ProfileController extends Controller
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
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * @throws LogicException
     * @throws ServerErrorHttpException
     */
    public function actionMy()
    {
        $pId = \Yii::$app->user->identity->getPId();
        $user = Users::findOne([\Yii::$app->user->getId()]);
        $personal = Personal::findOne([$pId]);
        if (!$user || !$personal) {
            throw new ServerErrorHttpException('Не удалось найти связанные модели для пользователя');
        }
        $personalContacts = new ProfileContactsForm($pId);
        $personalNotification = new ProfileNotificationsForm($pId);
        $personalAccounts = Personal::findAll(['user_id' => $user->id]);
        if (\Yii::$app->request->isPost) {
            $currentAvatar = $user->avatar;

            $user->load(\Yii::$app->request->post());
            $user->validate();
            $user->save();

            $uploadedFile = UploadedFile::getInstanceByName('profile_avatar');
            if ($uploadedFile) {
                $avatarLink = FileHelper::saveUploaded($uploadedFile, \Yii::getAlias('@user-avatars') . $user->id . '/', true);
                if ($avatarLink) {
                    $user->avatar = $avatarLink;
                } else {
                    \Yii::$app->session->setFlash('warning', 'Не удалось загрузить изображение. Попробуйте изменить изображение');
                }
            } else {
                $user->avatar = $currentAvatar;
            }
            $user->save();

            $personalNotification->load(\Yii::$app->request->post(), 'ProfileNotificationsForm');
            $personalNotification->save();
            try {
                $log = new PersonalLog();
                $log->createLog($pId ?? 0, '', PersonalLog::EDIT_PROFILE);
            } catch (\Exception $exception) {
            }
        }


        return $this->render('my', [
            'user' => $user,
            'pId' => $pId,
            'accounts' => $personalAccounts,
            'contacts' => $personalContacts,
            'notify' => $personalNotification,
        ]);
    }
}